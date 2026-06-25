<?php

namespace App\Observers;

use App\Models\Produk;
use App\Models\Notification;

class ProductObserver
{
    /**
     * Handle the Produk "updated" event.
     */
    public function updated(Produk $produk)
    {
        // Cek jika stok berkurang dan sudah menipis
        if ($produk->isDirty('stok')) {
            $oldStok = $produk->getOriginal('stok');
            $newStok = $produk->stok;

            // Jika stok habis
            if ($newStok == 0 && $oldStok > 0) {
                Notification::create([
                    'user_id' => null,
                    'type' => 'stock',
                    'title' => 'Stok Habis: ' . $produk->nama_produk,
                    'message' => 'Produk "' . $produk->nama_produk . '" telah habis. Segera lakukan restock!',
                    'link' => '/admin/product-management',
                    'priority' => 'urgent',
                    'is_read' => false,
                ]);
            }
            // Jika stok menipis (kurang dari 10)
            elseif ($newStok > 0 && $newStok < 10 && $oldStok >= 10) {
                Notification::create([
                    'user_id' => null,
                    'type' => 'stock',
                    'title' => 'Stok Menipis: ' . $produk->nama_produk,
                    'message' => 'Produk "' . $produk->nama_produk . '" tersisa ' . $newStok . ' unit. Pertimbangkan untuk restock.',
                    'link' => '/admin/product-management',
                    'priority' => 'important',
                    'is_read' => false,
                ]);
            }
        }
    }
}
