<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\ProductSizeStock;
use App\Models\Produk;
use App\Models\Notification;
use App\Models\OrderStatusHistory;
use App\Models\UserRewardRedemption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredOrdersStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:release-expired-stocks';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $signature_description = 'Release stocks for orders that passed payment deadline without verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired orders...');

        $expiredOrders = Order::with('orderItems')
            ->whereIn('payment_status', ['waiting_verification', 'rejected'])
            ->where('payment_deadline_at', '<', now())
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders found.');
            return 0;
        }

        $this->info('Found ' . $expiredOrders->count() . ' expired orders. Processing...');

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                // 1. Update order payment status to expired
                $order->payment_status = 'expired';
                $order->save();

                // 2. Track history
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'delivery_status_id' => $order->delivery_status_id, // keep current, but flag payment as expired
                    'changed_at' => now(),
                    'notes' => 'Pembayaran kedaluwarsa (otomatis dibatalkan oleh sistem)'
                ]);

                // 3. Restore stocks for each item
                foreach ($order->orderItems as $item) {
                    $size = strtoupper(trim((string)$item->ukuran));
                    
                    if ($size !== '') {
                        $variantStock = ProductSizeStock::where('produk_id', $item->produk_id)
                            ->where('ukuran', $size)
                            ->lockForUpdate()
                            ->first();

                        if ($variantStock) {
                            $variantStock->increment('stok', $item->qty);
                            
                            // Update global stock for the product
                            $product = Produk::find($item->produk_id);
                            if ($product) {
                                $product->stok = ProductSizeStock::where('produk_id', $product->id)->sum('stok');
                                $product->save();
                            }
                            
                            Log::info("Restored stock for Product #{$item->produk_id} Size {$size}: +{$item->qty}");
                        }
                    } else {
                        $product = Produk::find($item->produk_id);
                        if ($product) {
                            $product->increment('stok', $item->qty);
                            Log::info("Restored stock for Product #{$item->produk_id} (No Size): +{$item->qty}");
                        }
                    }
                }

                // 3b. Restore membership voucher if one was used in this order
                if ($order->membership_redemption_id) {
                    $redemption = UserRewardRedemption::find($order->membership_redemption_id);
                    if ($redemption) {
                        $redemption->update([
                            'status' => 'active',
                            'used_at' => null,
                        ]);
                        Log::info("Restored Voucher Redemption #{$order->membership_redemption_id} back to active for expired Order #{$order->id}");
                    }
                }

                // 4. Create customer notification
                Notification::create([
                    'user_id' => $order->user_id,
                    'type' => 'order',
                    'title' => 'Pesanan Kedaluwarsa #' . $order->id,
                    'message' => 'Pesanan Anda #' . $order->id . ' otomatis dibatalkan karena tidak ada pembayaran hingga melewati batas waktu.',
                    'link' => route('customer.profile.index'),
                    'priority' => 'high',
                    'is_read' => false,
                ]);

                $this->info("Order #{$order->id} ({$order->order_number}) marked as expired and stocks released.");
            });
        }

        $this->info('Stock release process completed.');
        return 0;
    }
}
