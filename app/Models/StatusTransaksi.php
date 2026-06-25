<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusTransaksi extends Model
{
    use HasFactory;

    protected $table = 'status_transaksi';

    public $timestamps = false;

    protected $fillable = [
        'nama_status',
        'keterangan',
    ];

    /**
     * Relasi ke Checkout
     */
    public function checkouts()
    {
        return $this->hasMany(Checkout::class, 'status_id');
    }
}
