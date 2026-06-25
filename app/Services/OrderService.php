<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Produk;
use App\Models\ProductSizeStock;
use App\Models\ShoppingCard;
use App\Models\UserRewardRedemption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Create order dari checkout data
     * 
     * @param array $checkoutData
     * @param string $paymentNumber
     * @return Order
     * @throws \Exception
     */
    public function createOrder(array $checkoutData, string $paymentNumber, ?string $paymentProof = null): Order
    {
        DB::beginTransaction();
        
        try {
            // Generate order number (unique untuk grouping items)
            $orderNumber = $this->generateOrderNumber();
            
            Log::info('Creating order:', ['order_number' => $orderNumber]);
            
            // 1. Create order (header)
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'alamat' => $checkoutData['alamat'],
                'metode_pengiriman_id' => $checkoutData['metode_pengiriman']['id'],
                'metode_pembayaran_id' => $checkoutData['metode_pembayaran']['id'],
                'delivery_status_id' => config('order.delivery_status.pending', 1),
                'total_ongkir' => $checkoutData['total_ongkir'],
                'total_bayar' => $checkoutData['total_bayar'],
                'payment_number' => $paymentNumber,
                'payment_status' => 'waiting_verification',
                'payment_proof' => $paymentProof,
                'payment_deadline_at' => $checkoutData['payment_deadline_at'] ?? now()->addHours(config('order.payment_deadline_hours', 24)),
                'membership_redemption_id' => $checkoutData['voucher']['redemption_id'] ?? null,
                'voucher_code' => $checkoutData['voucher']['code'] ?? null,
                'voucher_discount' => $checkoutData['voucher_discount'] ?? 0,
                'membership_tier' => $checkoutData['membership_tier'] ?? null,
                'membership_tier_discount' => $checkoutData['membership_tier_discount'] ?? 0,
                'created_at' => $checkoutData['created_at'] ?? now(),
            ]);
            
            Log::info('Order created:', ['order_id' => $order->id]);
            
            // 2. Create order items (detail)
            $this->createOrderItems($order, $checkoutData['products']);
            
            // 3. Reduce stock after order items are persisted
            $this->reduceProductStock($checkoutData['products']);

            // 4. Track status history
            $this->createStatusHistory($order->id, config('order.delivery_status.pending', 1));
            
            // 5. Mark membership voucher as used after order header is persisted
            $this->markMembershipVoucherUsed($order, $checkoutData['voucher'] ?? null);

            // 6. Clear shopping cart
            $this->clearCart($checkoutData['products']);
            
            DB::commit();
             
            return $order;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create order items
     */
    private function createOrderItems(Order $order, array $products): void
    {
        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'produk_id' => $product['produk_id'],
                'nama_produk' => $product['nama'],
                'ukuran' => $product['ukuran'] ?? null,
                'qty' => $product['qty'],
                'harga' => $product['harga'],
                'subtotal' => $product['subtotal'],
            ]);
            
            Log::info('Order item created:', [
                'order_id' => $order->id, 
                'product' => $product['nama']
            ]);
        }
    }
    
    /**
     * Create status history
     */
    private function createStatusHistory(int $orderId, int $statusId): void
    {
        OrderStatusHistory::create([
            'order_id' => $orderId,
            'delivery_status_id' => $statusId,
            'changed_at' => now(),
        ]);
        
        Log::info('Order status history created:', [
            'order_id' => $orderId, 
            'status_id' => $statusId
        ]);
    }

    private function reduceProductStock(array $products): void
    {
        $requestedByVariant = [];

        foreach ($products as $product) {
            $productId = $product['produk_id'];
            $size = strtoupper(trim((string) ($product['ukuran'] ?? '')));
            $key = $productId . '|' . $size;
            $requestedByVariant[$key]['product_id'] = $productId;
            $requestedByVariant[$key]['size'] = $size;
            $requestedByVariant[$key]['qty'] = ($requestedByVariant[$key]['qty'] ?? 0) + (int) $product['qty'];
            $requestedByVariant[$key]['name'] = $product['nama'] ?? $productId;
        }

        foreach ($requestedByVariant as $request) {
            $produk = Produk::whereKey($request['product_id'])->lockForUpdate()->first();
            $qty = $request['qty'];
            $size = $request['size'];

            if (!$produk) {
                throw new \Exception('Produk tidak ditemukan: ' . $request['name']);
            }

            $variantStock = $size !== ''
                ? ProductSizeStock::where('produk_id', $produk->id)
                    ->where('ukuran', $size)
                    ->lockForUpdate()
                    ->first()
                : null;

            if ($variantStock) {
                if ($qty > $variantStock->stok) {
                    throw new \Exception('Stok "' . $produk->nama_produk . '" ukuran ' . $size . ' tidak mencukupi. Stok tersedia: ' . $variantStock->stok . '.');
                }

                $variantStock->decrement('stok', $qty);
                $produk->forceFill([
                    'stok' => ProductSizeStock::where('produk_id', $produk->id)->sum('stok'),
                ])->save();
                continue;
            }

            if ($qty > $produk->stok) {
                throw new \Exception('Stok "' . $produk->nama_produk . '" tidak mencukupi. Stok tersedia: ' . $produk->stok . '.');
            }

            $produk->decrement('stok', $qty);
        }
    }

    public function grantMembershipRewardPoints(Order $order): void
    {
        $order->loadMissing('user');

        if (! $order->user || ! $order->user->isMemberActive()) {
            return;
        }

        $multiplier = $order->user->getTierPointsMultiplier();
        $points = (int) floor(((float) $order->total_bayar) / 10000 * $multiplier);

        if ($points <= 0) {
            return;
        }

        $order->user->addRewardPoints(
            $points,
            'Reward dari transaksi #' . $order->id . ' (Multiplier Tier ' . $multiplier . 'x)',
            $order->id
        );
    }

    private function markMembershipVoucherUsed(Order $order, ?array $voucher): void
    {
        if (! $voucher || empty($voucher['redemption_id'])) {
            return;
        }

        $redemption = UserRewardRedemption::whereKey($voucher['redemption_id'])
            ->where('user_id', $order->user_id)
            ->lockForUpdate()
            ->first();

        if (! $redemption || $redemption->status !== 'active' || $redemption->used_at) {
            throw new \Exception('Voucher membership sudah digunakan atau tidak valid.');
        }

        $redemption->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }
    
    /**
     * Clear shopping cart items
     */
    private function clearCart(array $products): void
    {
        $cartIds = array_column($products, 'cart_id');
        ShoppingCard::where('user_id', Auth::id())
            ->whereIn('id', $cartIds)
            ->delete();
    }
    
    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        $prefix = config('order.order_number_prefix', 'ORD');
        return $prefix . '-' . time() . '-' . Auth::id();
    }
    
    /**
     * Calculate order totals
     * 
     * @param array $products
     * @param float $shippingCost
     * @return array
     */
    public function calculateTotals(array $products, float $shippingCost): array
    {
        $subtotal = 0;
        
        foreach ($products as $product) {
            $subtotal += $product['subtotal'];
        }
        
        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $subtotal + $shippingCost,
        ];
    }
}
