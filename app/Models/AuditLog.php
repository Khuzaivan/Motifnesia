<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'details',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the audited model instance.
     */
    public function auditable()
    {
        if ($this->model_type && $this->model_id && class_exists($this->model_type)) {
            return $this->model_type::find($this->model_id);
        }

        return null;
    }
}
