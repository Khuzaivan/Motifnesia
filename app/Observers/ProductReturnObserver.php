<?php

namespace App\Observers;

use App\Models\ProductReturn;
use App\Models\Notification;

class ProductReturnObserver
{
    /**
     * Handle the ProductReturn "created" event.
     * Customer mengajukan retur → notif ke admin
     */
    public function created(ProductReturn $return)
    {
        // Notifikasi untuk admin
        Notification::create([
            'user_id' => null, // Untuk admin
            'type' => 'return',
            'title' => 'Permintaan Retur Baru #' . $return->id,
            'message' => 'Permintaan retur dari ' . $return->user->name . ' untuk produk ' . $return->produk->nama_produk,
            'link' => route('admin.returns.index'),
            'priority' => 'urgent',
            'is_read' => false,
        ]);

        // Notifikasi untuk customer
        Notification::create([
            'user_id' => $return->user_id,
            'type' => 'return',
            'title' => 'Permintaan Retur Dikirim',
            'message' => 'Permintaan retur Anda untuk produk ' . $return->produk->nama_produk . ' sedang diproses. Status: Pending',
            'link' => route('customer.returns.index'),
            'priority' => 'normal',
            'is_read' => false,
            'data' => [
                'return_id' => $return->id,
                'order_item_id' => $return->order_item_id,
                'produk_nama' => $return->produk->nama_produk,
                'produk_gambar' => $return->produk->gambar,
                'status' => 'Pending',
                'reason' => $return->reason,
            ],
        ]);
    }

    /**
     * Handle the ProductReturn "updated" event.
     * Admin update status → notif ke customer
     */
    public function updated(ProductReturn $return)
    {
        // Hanya kirim notif jika status berubah
        if ($return->isDirty('status')) {
            $statusMessages = [
                'Pending' => 'Permintaan retur Anda sedang ditinjau.',
                'Disetujui' => 'Permintaan retur Anda telah disetujui! Silakan tunggu proses selanjutnya.',
                'Ditolak' => 'Permintaan retur Anda ditolak. Alasan: ' . ($return->admin_note ?? 'Tidak memenuhi syarat'),
                'Diproses' => 'Retur Anda sedang diproses. Refund akan segera ditransfer.',
                'Selesai' => 'Proses retur selesai! Refund Rp ' . number_format($return->refund_amount, 0, ',', '.') . ' telah ditransfer.',
            ];

            $priority = match($return->status) {
                'Disetujui' => 'important',
                'Ditolak' => 'urgent',
                'Selesai' => 'important',
                default => 'normal',
            };

            // Notifikasi untuk customer
            Notification::create([
                'user_id' => $return->user_id,
                'type' => 'return',
                'title' => 'Update Status Retur #' . $return->id,
                'message' => $statusMessages[$return->status] ?? 'Status retur Anda: ' . $return->status,
                'link' => route('customer.returns.index'),
                'priority' => $priority,
                'is_read' => false,
                'data' => [
                    'return_id' => $return->id,
                    'order_item_id' => $return->order_item_id,
                    'produk_nama' => $return->produk->nama_produk,
                    'produk_gambar' => $return->produk->gambar,
                    'status' => $return->status,
                    'reason' => $return->reason,
                    'admin_note' => $return->admin_note,
                    'refund_amount' => $return->refund_amount,
                ],
            ]);
        }
    }

    /**
     * Handle the ProductReturn "deleted" event.
     */
    public function deleted(ProductReturn $return)
    {
        // Optional: Notifikasi jika retur dihapus oleh admin
    }
}
