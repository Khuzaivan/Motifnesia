<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'points_required',
        'discount_type',
        'discount_value',
        'max_discount_value',
        'is_active',
    ];

    protected $casts = [
        'points_required' => 'integer',
        'discount_value' => 'integer',
        'max_discount_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function redemptions()
    {
        return $this->hasMany(UserRewardRedemption::class, 'reward_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getDiscountLabelAttribute(): string
    {
        return match ($this->discount_type) {
            'fixed' => 'Potongan Rp ' . number_format($this->discount_value, 0, ',', '.'),
            'percent' => 'Diskon ' . $this->discount_value . '%' . ($this->max_discount_value ? ' (Maks. Rp ' . number_format($this->max_discount_value, 0, ',', '.') . ')' : ''),
            'free_shipping' => 'Gratis Ongkir',
            default => 'Promo Member',
        };
    }
}
