<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipReward;
use App\Models\Notification;
use App\Models\PointTransaction;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\MemberNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MembershipController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:is-owner'),
        ];
    }

    public function index()
    {
        $members = User::where('is_member', true)
            ->withCount(['pointTransactions', 'rewardRedemptions'])
            ->orderByDesc('membership_joined_at')
            ->paginate(20);

        return view('admin.pages.memberships.index', [
            'members' => $members,
            'activePage' => 'memberships',
        ]);
    }

    public function updateMemberStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'membership_status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $member = User::findOrFail($id);
        $member->update([
            'is_member' => true,
            'membership_status' => $validated['membership_status'],
            'membership_joined_at' => $member->membership_joined_at ?: now(),
        ]);

        Notification::create([
            'user_id' => $member->id,
            'type' => 'membership_status_updated',
            'title' => 'Status Membership Diperbarui',
            'message' => 'Status membership Anda sekarang: ' . ucfirst($validated['membership_status']) . '.',
            'link' => route('customer.membership.index'),
            'priority' => 'important',
            'is_read' => false,
            'data' => [
                'membership_status' => $validated['membership_status'],
            ],
        ]);

        AuditLogService::log('update_member_status', $member, null, [
            'new_status' => $validated['membership_status'],
        ]);

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Status member berhasil diperbarui.');
    }

    public function adjustPoints(Request $request, $id)
    {
        $validated = $request->validate([
            'points' => ['required', 'integer', 'not_in:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $member = User::findOrFail($id);
        $delta = (int) $validated['points'];
        $description = $validated['description'] ?: 'Penyesuaian poin oleh admin';

        DB::transaction(function () use ($member, $delta, $description) {
            $lockedMember = User::whereKey($member->id)->lockForUpdate()->firstOrFail();
            $lockedMember->reward_points = max(0, (int) $lockedMember->reward_points + $delta);
            $lockedMember->save();

            PointTransaction::create([
                'user_id' => $lockedMember->id,
                'order_id' => null,
                'type' => 'adjust',
                'points' => $delta,
                'description' => $description,
            ]);

            Notification::create([
                'user_id' => $lockedMember->id,
                'type' => 'membership_points_adjusted',
                'title' => 'Poin Membership Disesuaikan',
                'message' => 'Admin menyesuaikan poin Anda sebesar ' . ($delta > 0 ? '+' : '') . $delta . ' poin.',
                'link' => route('customer.membership.history'),
                'priority' => 'info',
                'is_read' => false,
                'data' => [
                    'points' => $delta,
                    'description' => $description,
                ],
            ]);
        });

        AuditLogService::log('adjust_member_points', $member, null, [
            'delta' => $delta,
            'description' => $description,
        ]);

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Poin member berhasil disesuaikan.');
    }

    public function rewards()
    {
        $rewards = MembershipReward::withCount('redemptions')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.pages.memberships.rewards', [
            'rewards' => $rewards,
            'activePage' => 'membership-rewards',
        ]);
    }

    public function createReward()
    {
        return view('admin.pages.memberships.rewardForm', [
            'reward' => new MembershipReward(),
            'formTitle' => 'Tambah Reward Membership',
            'activePage' => 'membership-rewards',
        ]);
    }

    public function storeReward(Request $request)
    {
        $data = $this->validatedRewardData($request);

        $reward = MembershipReward::create($data);

        AuditLogService::log('create_reward', $reward);

        return redirect()->route('admin.membership-rewards.index')
            ->with('success', 'Reward membership berhasil ditambahkan.');
    }

    public function editReward($id)
    {
        $reward = MembershipReward::findOrFail($id);

        return view('admin.pages.memberships.rewardForm', [
            'reward' => $reward,
            'formTitle' => 'Edit Reward Membership',
            'activePage' => 'membership-rewards',
        ]);
    }

    public function updateReward(Request $request, $id)
    {
        $reward = MembershipReward::findOrFail($id);
        $reward->update($this->validatedRewardData($request));

        AuditLogService::log('update_reward', $reward);

        return redirect()->route('admin.membership-rewards.index')
            ->with('success', 'Reward membership berhasil diperbarui.');
    }

    public function destroyReward($id)
    {
        $reward = MembershipReward::findOrFail($id);
        $reward->update(['is_active' => false]);

        AuditLogService::log('deactivate_reward', $reward);

        return redirect()->route('admin.membership-rewards.index')
            ->with('success', 'Reward membership berhasil dinonaktifkan.');
    }

    public function broadcastForm()
    {
        $members = User::where('is_member', true)
            ->where('membership_status', 'active')
            ->orderBy('full_name')
            ->orderBy('name')
            ->paginate(20);

        $broadcastMessage = session('broadcast_message', '');
        $broadcastCaption = session('broadcast_caption', '');

        return view('admin.pages.memberships.broadcast', [
            'members' => $members,
            'broadcastMessage' => $broadcastMessage,
            'broadcastCaption' => $broadcastCaption,
            'activePage' => 'membership-broadcast',
        ]);
    }

    public function broadcast(Request $request, MemberNotificationService $notifier)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'caption' => ['nullable', 'string', 'max:1000'],
        ]);

        $members = User::where('is_member', true)
            ->where('membership_status', 'active')
            ->get();

        $emailsSent = 0;
        $caption = $validated['caption'] ?: 'Promo khusus ini dikirim otomatis melalui email untuk member aktif Motifnesia.';

        DB::transaction(function () use ($members, $validated, $caption, $notifier, &$emailsSent) {
            foreach ($members as $member) {
                $result = $notifier->sendVoucherNotice($member, $validated['title'], $validated['message'], route('customer.membership.index'), [
                    'type' => 'member_special_promo',
                    'caption' => $caption,
                    'broadcasted_at' => now()->toDateTimeString(),
                ]);

                if ($result['email_sent']) {
                    $emailsSent++;
                }
            }
        });

        $mailer = config('mail.default');
        $mailMessage = in_array($mailer, ['log', 'array'], true)
            ? 'Mailer masih mode ' . $mailer . ', jadi email baru tercatat di log Laravel dan belum masuk inbox.'
            : 'Email otomatis terkirim ke ' . $emailsSent . ' member.';

        return redirect()->route('admin.membership-broadcast.index')
            ->with('success', 'Promo member berhasil diproses untuk ' . $members->count() . ' member aktif. ' . $mailMessage)
            ->with('broadcast_message', $validated['message'])
            ->with('broadcast_caption', $caption);
    }

    private function validatedRewardData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'points_required' => ['required', 'integer', 'min:1'],
            'discount_type' => ['required', Rule::in(['fixed', 'percent', 'free_shipping'])],
            'discount_value' => ['required', 'integer', 'min:0'],
            'max_discount_value' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        if ($data['discount_type'] === 'free_shipping') {
            $data['discount_value'] = 0;
            $data['max_discount_value'] = null;
        }

        if ($data['discount_type'] === 'fixed') {
            $data['max_discount_value'] = null;
        }

        if ($data['discount_type'] === 'percent') {
            if ($data['discount_value'] > 100) {
                throw ValidationException::withMessages([
                    'discount_value' => 'Nilai diskon persen maksimal 100.',
                ]);
            }
        }

        return $data;
    }

}
