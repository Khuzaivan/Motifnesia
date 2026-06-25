<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MemberNotificationService
{
    public function sendVoucherNotice(User $member, string $title, string $message, ?string $link = null, array $data = [], bool $createInternal = true): array
    {
        if ($createInternal) {
            Notification::create([
                'user_id' => $member->id,
                'type' => $data['type'] ?? 'member_voucher_notice',
                'title' => $title,
                'message' => $message,
                'link' => $link ?: route('customer.membership.index'),
                'priority' => 'important',
                'is_read' => false,
                'data' => $data,
            ]);
        }

        $this->sendLiveChatNotice($member, $title, $message);
        $emailBody = $this->buildEmailBody($member, $title, $message, $link, $data);
        $emailSent = $this->sendEmailNotice($member, $title, $emailBody);

        return [
            'email_sent' => $emailSent,
        ];
    }

    private function sendLiveChatNotice(User $member, string $title, string $message): void
    {
        $adminId = Auth::user()?->role === 'admin'
            ? Auth::id()
            : User::where('role', 'admin')->value('id');

        if (! $adminId) {
            return;
        }

        $chat = Chat::firstOrCreate(
            ['user_id' => $member->id, 'status' => 'active'],
            ['admin_id' => $adminId, 'subject' => 'Membership Motifnesia', 'last_message_at' => now()]
        );

        $chat->update([
            'admin_id' => $chat->admin_id ?: $adminId,
            'last_message_at' => now(),
        ]);

        ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $adminId,
            'message' => $title . "\n" . $message,
            'is_read' => false,
        ]);
    }

    private function buildEmailBody(User $member, string $title, string $message, ?string $link = null, array $data = []): string
    {
        $name = $member->full_name ?: $member->name ?: 'Member Motifnesia';
        $caption = trim((string) ($data['caption'] ?? $data['description'] ?? 'Informasi ini dikirim otomatis untuk member aktif Motifnesia.'));
        $lines = [
            'Halo ' . $name . ',',
            '',
            $title,
            '',
            $message,
        ];

        if ($caption !== '') {
            $lines[] = '';
            $lines[] = 'Keterangan:';
            $lines[] = $caption;
        }

        if (! empty($data['voucher_code'])) {
            $lines[] = '';
            $lines[] = 'Kode voucher: ' . $data['voucher_code'];
        }

        if ($link) {
            $lines[] = '';
            $lines[] = 'Link terkait:';
            $lines[] = $link;
        }

        $lines[] = '';
        $lines[] = 'Email ini dikirim otomatis oleh Motifnesia.';

        return implode("\n", $lines);
    }

    private function sendEmailNotice(User $member, string $title, string $body): bool
    {
        if (! $member->email) {
            return false;
        }

        $deliverableMailer = ! in_array(config('mail.default'), ['log', 'array'], true);

        try {
            Mail::raw($body, function ($mail) use ($member, $title) {
                $mail->to($member->email)
                    ->subject($title);
            });

            return $deliverableMailer;
        } catch (\Throwable $e) {
            Log::warning('Member email notification failed: ' . $e->getMessage(), [
                'user_id' => $member->id,
            ]);

            return false;
        }
    }
}
