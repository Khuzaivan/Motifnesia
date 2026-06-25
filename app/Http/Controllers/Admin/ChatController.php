<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ChatController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:is-kasir'),
        ];
    }

    /**
     * Menampilkan halaman Live Chat Support.
     */
    public function index(Request $request)
    {
        // Get all active chats, sorted by last message
        $chats = Chat::with(['user', 'lastMessage'])
            ->where('status', 'active')
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Get selected chat (default to first chat)
        $selectedChatId = $request->query('chat_id', $chats->first()->id ?? null);
        $currentChat = $selectedChatId ? Chat::with(['messages.sender', 'user'])->find($selectedChatId) : null;

        return view('admin.pages.liveChatSupport', [
            'chats' => $chats,
            'currentChat' => $currentChat,
            'currentChatMessages' => $currentChat ? $currentChat->messages : collect([]),
            'currentChatName' => $currentChat ? $currentChat->user->name : '',
            'activePage' => 'live-chat'
        ]);
    }

    /**
     * Send message from admin
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required|string|max:1000',
        ]);

        $admin = Auth::user();
        $chat = Chat::findOrFail($request->chat_id);

        // Simpan message
        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $admin->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Update chat admin_id dan last_message_at
        $chat->update([
            'admin_id' => $admin->id,
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'created_at' => $message->created_at->format('H:i'),
                'sender_name' => $admin->name,
                'is_admin' => true,
            ],
        ]);
    }

    /**
     * Get new messages (Polling)
     */
    public function getNewMessages(Request $request, $chatId)
    {
        $lastMessageId = $request->query('last_message_id', 0);

        $newMessages = ChatMessage::where('chat_id', $chatId)
            ->where('id', '>', $lastMessageId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark customer messages as read
        $admin = Auth::user();
        ChatMessage::where('chat_id', $chatId)
            ->where('sender_id', '!=', $admin->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $newMessages->map(function ($msg) use ($admin) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->format('H:i'),
                    'sender_name' => $msg->sender->name,
                    'is_admin' => $msg->sender_id === $admin->id,
                ];
            }),
        ]);
    }

    /**
     * Get chat list updates
     */
    public function getChatList()
    {
        $chats = Chat::with(['user', 'lastMessage'])
            ->where('status', 'active')
            ->orderBy('last_message_at', 'desc')
            ->get();

        $admin = Auth::user();

        return response()->json([
            'success' => true,
            'chats' => $chats->map(function ($chat) use ($admin) {
                $unreadCount = $chat->messages()
                    ->where('sender_id', '!=', $admin->id)
                    ->where('is_read', false)
                    ->count();

                return [
                    'id' => $chat->id,
                    'user_name' => $chat->user->name,
                    'last_message' => $chat->lastMessage ? $chat->lastMessage->message : '',
                    'unread_count' => $unreadCount,
                    'last_message_at' => $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '',
                ];
            }),
        ]);
    }
}
