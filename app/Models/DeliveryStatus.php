<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryStatus extends Model
{
    protected $table = 'delivery_status';
    
    protected $fillable = [
        'nama_status',
        'deskripsi',
    ];

    public function checkouts()
    {
        return $this->hasMany(Checkout::class, 'delivery_status_id');
    }
}
