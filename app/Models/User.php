<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\AssetUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'email',
        'password',
        'role',
        'admin_role',
        'account_status',
        'account_status_reason',
        'account_status_changed_at',
        'account_status_changed_by',
        'profile_pic',
        'phone_number',
        'birth_date',
        'gender',
        'address_line',
        'city',
        'province',
        'postal_code',
        'secret_question',
        'secret_answer',
        'is_member',
        'membership_status',
        'membership_joined_at',
        'reward_points',
        'membership_tier',
        'total_spending',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'account_status_changed_at' => 'datetime',
            'is_member' => 'boolean',
            'membership_joined_at' => 'datetime',
            'reward_points' => 'integer',
            'total_spending' => 'decimal:2',
        ];
    }

    // Relationship dengan orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relationship dengan addresses
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    // Get primary address
    public function primaryAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_primary', true);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function rewardRedemptions()
    {
        return $this->hasMany(UserRewardRedemption::class);
    }

    public function supplierProfile()
    {
        return $this->hasOne(Supplier::class);
    }

    public function isMemberActive(): bool
    {
        return (bool) $this->is_member && $this->membership_status === 'active';
    }

    public function addRewardPoints(int $points, string $description, ?int $orderId = null): ?PointTransaction
    {
        if ($points <= 0) {
            return null;
        }

        return DB::transaction(function () use ($points, $description, $orderId) {
            $user = self::whereKey($this->id)->lockForUpdate()->first();

            if (! $user || ! $user->isMemberActive()) {
                return null;
            }

            if ($orderId && PointTransaction::where('user_id', $user->id)
                ->where('order_id', $orderId)
                ->where('type', 'earn')
                ->exists()) {
                return null;
            }

            $user->reward_points = max(0, (int) $user->reward_points) + $points;
            $user->save();

            $transaction = PointTransaction::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'type' => 'earn',
                'points' => $points,
                'description' => $description,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'membership_point_earned',
                'title' => 'Poin Member Bertambah',
                'message' => 'Anda mendapatkan ' . $points . ' poin dari transaksi ini.',
                'link' => route('customer.membership.index'),
                'priority' => 'info',
                'is_read' => false,
                'data' => [
                    'order_id' => $orderId,
                    'points' => $points,
                ],
            ]);

            $this->forceFill([
                'reward_points' => $user->reward_points,
                'is_member' => $user->is_member,
                'membership_status' => $user->membership_status,
                'membership_joined_at' => $user->membership_joined_at,
            ]);

            return $transaction;
        });
    }

    public function redeemReward(MembershipReward $reward): UserRewardRedemption
    {
        return DB::transaction(function () use ($reward) {
            $user = self::whereKey($this->id)->lockForUpdate()->firstOrFail();

            if (! $user->isMemberActive()) {
                throw new \RuntimeException('Membership belum aktif.');
            }

            if (! $reward->is_active) {
                throw new \RuntimeException('Reward ini sedang tidak aktif.');
            }

            if ((int) $user->reward_points < (int) $reward->points_required) {
                throw new \RuntimeException('Poin tidak cukup untuk menukar voucher ini.');
            }

            $user->reward_points = max(0, (int) $user->reward_points - (int) $reward->points_required);
            $user->save();

            PointTransaction::create([
                'user_id' => $user->id,
                'order_id' => null,
                'type' => 'redeem',
                'points' => $reward->points_required,
                'description' => 'Penukaran poin untuk ' . $reward->title,
            ]);

            $redemption = UserRewardRedemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'voucher_code' => $this->generateMembershipVoucherCode($user->id),
                'points_used' => $reward->points_required,
                'status' => 'active',
                'redeemed_at' => now(),
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'membership_reward_redeemed',
                'title' => 'Voucher Member Berhasil Ditukar',
                'message' => 'Voucher ' . $reward->title . ' berhasil dibuat dengan kode ' . $redemption->voucher_code . '.',
                'link' => route('customer.membership.vouchers'),
                'priority' => 'important',
                'is_read' => false,
                'data' => [
                    'reward_id' => $reward->id,
                    'voucher_code' => $redemption->voucher_code,
                    'points_used' => $reward->points_required,
                ],
            ]);

            $this->forceFill(['reward_points' => $user->reward_points]);

            return $redemption;
        });
    }

    private function generateMembershipVoucherCode(int $userId): string
    {
        do {
            $code = 'MEMBER-' . $userId . '-' . Str::upper(Str::random(8));
        } while (UserRewardRedemption::where('voucher_code', $code)->exists());

        return $code;
    }

    public function updateSpendingAndTier(): void
    {
        if (!$this->isMemberActive()) {
            return;
        }

        $total = (float) $this->orders()->where('payment_status', 'verified')->sum('total_bayar');

        $this->total_spending = $total;
        $this->calculateMembershipTier();
        $this->save();
    }

    public function calculateMembershipTier(): void
    {
        if (!$this->isMemberActive()) {
            $this->membership_tier = 'bronze';
            return;
        }

        $spending = (float) $this->total_spending;
        if ($spending >= 2000000) {
            $this->membership_tier = 'gold';
        } elseif ($spending >= 500000) {
            $this->membership_tier = 'silver';
        } else {
            $this->membership_tier = 'bronze';
        }
    }

    public function getTierDiscount(): float
    {
        if (!$this->isMemberActive()) {
            return 0.00;
        }

        switch ($this->membership_tier) {
            case 'gold':
                return 0.05;
            case 'silver':
                return 0.03;
            case 'bronze':
            default:
                return 0.00;
        }
    }

    public function getTierPointsMultiplier(): float
    {
        if (!$this->isMemberActive()) {
            return 1.0;
        }

        switch ($this->membership_tier) {
            case 'gold':
                return 2.0;
            case 'silver':
                return 1.5;
            case 'bronze':
            default:
                return 1.0;
        }
    }

    public function getMembershipTierInfoAttribute(): array
    {
        $tier = $this->membership_tier ?: 'bronze';
        $spending = (float) $this->total_spending;

        $tiers = [
            'bronze' => [
                'name' => 'Bronze',
                'badge' => 'Bronze 🥉',
                'color' => '#CD7F32',
                'discount' => 0.00,
                'multiplier' => 1.0,
                'min_spend' => 0,
                'next_tier' => 'silver',
                'next_spend_required' => 500000,
            ],
            'silver' => [
                'name' => 'Silver',
                'badge' => 'Silver 🥈',
                'color' => '#C0C0C0',
                'discount' => 0.03,
                'multiplier' => 1.5,
                'min_spend' => 500000,
                'next_tier' => 'gold',
                'next_spend_required' => 2000000,
            ],
            'gold' => [
                'name' => 'Gold',
                'badge' => 'Gold 🥇',
                'color' => '#FFD700',
                'discount' => 0.05,
                'multiplier' => 2.0,
                'min_spend' => 2000000,
                'next_tier' => null,
                'next_spend_required' => null,
            ],
        ];

        $currentInfo = $tiers[$tier] ?? $tiers['bronze'];

        if ($currentInfo['next_spend_required'] !== null) {
            $needed = $currentInfo['next_spend_required'] - $spending;
            $progress = ($spending - $currentInfo['min_spend']) / ($currentInfo['next_spend_required'] - $currentInfo['min_spend']) * 100;
            $progress = max(0, min(100, $progress));
        } else {
            $needed = 0;
            $progress = 100;
        }

        return array_merge($currentInfo, [
            'current_spending' => $spending,
            'needed_spending' => max(0, $needed),
            'progress_percentage' => $progress,
        ]);
    }

    public function getProfilePicUrlAttribute(): string
    {
        return AssetUrl::profile($this->profile_pic, $this->full_name ?: $this->name);
    }
}
