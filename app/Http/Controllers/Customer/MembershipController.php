<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MembershipReward;
use App\Models\Notification;
use App\Services\MemberNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MembershipController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $rewards = MembershipReward::active()
            ->orderBy('points_required')
            ->get();

        $pointTransactions = $user->pointTransactions()
            ->with('order')
            ->latest()
            ->limit(8)
            ->get();

        $vouchers = $user->rewardRedemptions()
            ->with('reward')
            ->latest()
            ->limit(6)
            ->get();

        return view('customer.pages.membership', compact(
            'user',
            'rewards',
            'pointTransactions',
            'vouchers'
        ));
    }

    public function register(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:30'],
        ], [
            'phone.required' => 'Nomor telepon wajib diisi untuk daftar membership.',
        ]);

        $phone = preg_replace('/\s+/', '', $validated['phone']);

        $user->update([
            'full_name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $phone,
            'is_member' => true,
            'membership_status' => 'active',
            'membership_joined_at' => $user->membership_joined_at ?: now(),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'membership_registered',
            'title' => 'Membership Aktif',
            'message' => 'Selamat, membership Motifnesia Anda sudah aktif.',
            'link' => route('customer.membership.index'),
            'priority' => 'important',
            'is_read' => false,
            'data' => [
                'phone' => $phone,
                'joined_at' => now()->toDateTimeString(),
            ],
        ]);

        return redirect()->route('customer.membership.index')
            ->with('success', 'Membership berhasil diaktifkan. Anda sudah bisa mengumpulkan poin reward.');
    }

    public function redeem($rewardId, MemberNotificationService $notifier)
    {
        $user = Auth::user();

        if (! $user->isMemberActive()) {
            return redirect()->route('customer.membership.index')
                ->with('error', 'Aktifkan membership terlebih dahulu sebelum menukar poin.');
        }

        $reward = MembershipReward::active()->findOrFail($rewardId);

        try {
            $redemption = $user->redeemReward($reward);
        } catch (\Throwable $e) {
            return redirect()->route('customer.membership.index')
                ->with('error', $e->getMessage() ?: 'Gagal menukar poin.');
        }

        $notifier->sendVoucherNotice(
            $user,
            'Voucher Member Baru',
            'Voucher ' . $redemption->voucher_code . ' berhasil dibuat dan bisa digunakan saat checkout.',
            route('customer.membership.vouchers'),
            [
                'type' => 'membership_reward_redeemed',
                'redemption_id' => $redemption->id,
                'reward_id' => $reward->id,
                'voucher_code' => $redemption->voucher_code,
                'caption' => 'Voucher member berhasil ditukar dari poin reward. Gunakan kode voucher ini saat checkout sebelum masa berlaku berakhir.',
            ],
            false
        );

        return redirect()->route('customer.membership.vouchers')
            ->with('success', 'Voucher berhasil dibuat: ' . $redemption->voucher_code);
    }

    public function history()
    {
        $user = Auth::user();

        if (! $user->isMemberActive()) {
            return redirect()->route('customer.membership.index')
                ->with('error', 'Aktifkan membership untuk melihat riwayat poin.');
        }

        $pointTransactions = $user->pointTransactions()
            ->with('order')
            ->latest()
            ->paginate(15);

        return view('customer.pages.membershipHistory', compact('user', 'pointTransactions'));
    }

    public function myVouchers()
    {
        $user = Auth::user();

        if (! $user->isMemberActive()) {
            return redirect()->route('customer.membership.index')
                ->with('error', 'Aktifkan membership untuk melihat voucher.');
        }

        $vouchers = $user->rewardRedemptions()
            ->with('reward')
            ->latest()
            ->paginate(12);

        return view('customer.pages.membershipVouchers', compact('user', 'vouchers'));
    }
}
