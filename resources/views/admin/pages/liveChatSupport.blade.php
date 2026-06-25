@extends('admin.layouts.mainLayout')

@section('title', 'Live Chat Support')

@section('content')
<div class="flex flex-col h-[calc(100vh-104px)] gap-0">
    <!-- Chat Container -->
    <div class="flex gap-6 h-full">
        <!-- Left Panel: Chat List -->
        <div class="w-80 glass-card rounded-3xl overflow-hidden flex flex-col shrink-0">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-5 py-4 flex items-center justify-between">
                <h2 class="text-white font-bold text-base font-['Plus_Jakarta_Sans'] flex items-center gap-2">
                    <i class="ri-customer-service-2-line text-xl"></i> Customer Chats
                </h2>
                <span class="bg-white/20 text-white text-xs font-bold px-2.5 py-1 rounded-full border border-white/30">
                    {{ $chats->count() }} Aktif
                </span>
            </div>
            
            <div class="flex-1 overflow-y-auto custom-scrollbar" id="chatListPanel">
                @if($chats->isEmpty())
                    <div class="flex flex-col items-center justify-center h-full text-slate-500 p-6">
                        <i class="ri-chat-off-line text-5xl mb-3 opacity-50"></i>
                        <p class="text-center text-sm font-medium">Belum ada chat masuk</p>
                    </div>
                @else
                    @foreach($chats as $chat)
                        <a href="{{ route('admin.chat.index', ['chat_id' => $chat->id]) }}" 
                           class="block p-4 border-b border-white/5 hover:bg-white/5 transition-colors {{ $currentChat && $currentChat->id === $chat->id ? 'bg-amber-500/10 border-l-4 border-l-amber-500' : '' }}">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-md border border-white/10">
                                    {{ strtoupper(substr($chat->user->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 class="font-semibold text-slate-200 text-sm truncate">{{ $chat->user->name }}</h3>
                                        @php
                                            $unreadCount = $chat->messages()->where('sender_id', '!=', Auth::id())->where('is_read', false)->count();
                                        @endphp
                                        @if($unreadCount > 0)
                                            <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0 ml-1 animate-pulse">{{ $unreadCount }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 truncate">{{ $chat->lastMessage ? \Illuminate\Support\Str::limit(str_replace(["\r", "\n"], ' ', $chat->lastMessage->message), 48) : 'Belum ada pesan' }}</p>
                                    <p class="text-[10px] text-slate-600 mt-1">{{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '' }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Right Panel: Chat Messages -->
        <div class="flex-1 glass-card rounded-3xl overflow-hidden flex flex-col">
            @if($currentChat)
                <!-- Chat Header -->
                <div class="bg-gradient-to-r from-slate-800/80 to-slate-700/80 border-b border-white/5 px-6 py-4 flex items-center justify-between backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold shadow-md border border-white/10">
                            {{ strtoupper(substr($currentChatName, 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-white font-bold font-['Plus_Jakarta_Sans'] leading-tight">{{ $currentChatName }}</h2>
                            <p class="text-slate-400 text-xs">{{ $currentChat->user->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="flex items-center gap-1.5 text-emerald-400 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Online
                        </span>
                    </div>
                </div>

                <!-- Messages Area -->
                <div id="chatMessages" class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-slate-900/30">
                    @if($currentChatMessages->isEmpty())
                        <div class="flex flex-col items-center justify-center h-full text-slate-600">
                            <i class="ri-chat-3-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Belum ada pesan di sini</p>
                        </div>
                    @else
                        @foreach($currentChatMessages as $message)
                            @php
                                $isAdmin = $message->sender_id === Auth::id();
                            @endphp
                            <div class="mb-4 flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                                <div class="max-w-[70%]">
                                    @if(!$isAdmin)
                                        <div class="flex items-end gap-2">
                                            <div class="w-8 h-8 rounded-xl bg-slate-700 border border-white/10 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold shadow">
                                                {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="bg-slate-700 border border-white/10 rounded-2xl rounded-bl-none px-4 py-2.5 shadow-md">
                                                    <p class="text-slate-200 text-sm leading-relaxed">{!! nl2br(e($message->message)) !!}</p>
                                                </div>
                                                <p class="text-[10px] text-slate-600 mt-1 ml-2">{{ $message->created_at->format('H:i') }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-end">
                                            <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl rounded-br-none px-4 py-2.5 shadow-md shadow-amber-500/20">
                                                <p class="text-white text-sm leading-relaxed">{!! nl2br(e($message->message)) !!}</p>
                                            </div>
                                            <p class="text-[10px] text-slate-600 mt-1 mr-2">{{ $message->created_at->format('H:i') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Input Area -->
                <div class="p-4 border-t border-white/5 bg-slate-800/50 backdrop-blur-sm">
                    <form id="chatForm" class="flex gap-3">
                        @csrf
                        <input type="hidden" id="chatId" value="{{ $currentChat->id }}">
                        <input 
                            type="text" 
                            id="messageInput" 
                            placeholder="Ketik balasan..."
                            autocomplete="off"
                            class="flex-1 px-5 py-3 bg-slate-900 border border-white/10 rounded-xl focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-slate-200 placeholder-slate-500 text-sm transition-colors"
                            required
                        >
                        <button 
                            type="submit"
                            class="px-5 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-amber-500/30 transition-all flex items-center gap-2 text-sm hover:scale-[1.02]"
                        >
                            <span>Kirim</span>
                            <i class="ri-send-plane-fill"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-slate-600">
                    <i class="ri-chat-3-line text-7xl mb-4 opacity-30"></i>
                    <p class="text-lg font-semibold font-['Plus_Jakarta_Sans'] text-slate-400">Pilih chat untuk memulai</p>
                    <p class="text-sm text-slate-600 mt-1">Klik nama pelanggan di panel kiri</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($currentChat)
<script>
let chatId = {{ $currentChat->id }};
let lastMessageId = {{ $currentChatMessages->last()->id ?? 0 }};
const originalPageTitle = document.title;

function showChatInterrupt(text) {
    let alertBox = document.getElementById('chatInterruptAlert');
    if (!alertBox) {
        alertBox = document.createElement('div');
        alertBox.id = 'chatInterruptAlert';
        alertBox.className = 'fixed right-6 bottom-6 z-50 bg-slate-900 border border-amber-500/30 text-slate-100 px-4 py-3 rounded-xl shadow-2xl text-sm font-bold';
        document.body.appendChild(alertBox);
    }

    alertBox.textContent = text;
    alertBox.style.display = 'block';
    document.title = 'Chat baru - ' + originalPageTitle;
    setTimeout(() => alertBox.style.display = 'none', 3200);
}

document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        document.title = originalPageTitle;
    }
});

function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function formatMessageText(text) {
    return String(text || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/\n/g, '<br>');
}

function addMessage(message, isAdmin) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `mb-4 flex ${isAdmin ? 'justify-end' : 'justify-start'}`;
    messageDiv.setAttribute('data-message-id', message.id);
    
    if (!isAdmin) {
        messageDiv.innerHTML = `
            <div class="max-w-[70%]">
                <div class="flex items-end gap-2">
                    <div class="w-8 h-8 rounded-xl bg-slate-700 border border-white/10 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold shadow">
                        ${message.sender_name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div class="bg-slate-700 border border-white/10 rounded-2xl rounded-bl-none px-4 py-2.5 shadow-md">
                            <p class="text-slate-200 text-sm leading-relaxed">${formatMessageText(message.message)}</p>
                        </div>
                        <p class="text-[10px] text-slate-600 mt-1 ml-2">${message.created_at}</p>
                    </div>
                </div>
            </div>`;
    } else {
        messageDiv.innerHTML = `
            <div class="max-w-[70%]">
                <div class="flex flex-col items-end">
                    <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl rounded-br-none px-4 py-2.5 shadow-md shadow-amber-500/20">
                        <p class="text-white text-sm leading-relaxed">${formatMessageText(message.message)}</p>
                    </div>
                    <p class="text-[10px] text-slate-600 mt-1 mr-2">${message.created_at}</p>
                </div>
            </div>`;
    }
    
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
    lastMessageId = message.id;
}

document.getElementById('chatForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    if (!message) return;
    
    try {
        const response = await fetch('{{ route("admin.chat.send") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ chat_id: chatId, message: message })
        });
        const data = await response.json();
        if (data.success) { addMessage(data.message, true); messageInput.value = ''; }
    } catch (error) { console.error('Error sending message:', error); }
});

async function pollMessages() {
    try {
        const response = await fetch(`{{ url('/admin/live-chat') }}/${chatId}/messages?last_message_id=${lastMessageId}`);
        const data = await response.json();
        if (data.success && data.messages.length > 0) {
            if (data.messages.some(msg => !msg.is_admin)) {
                showChatInterrupt('Pesan baru dari customer');
            }
            data.messages.forEach(msg => addMessage(msg, msg.is_admin));
        }
    } catch (error) { console.error('Error polling:', error); }
}

async function pollChatList() {
    try {
        const response = await fetch('{{ route("admin.chat.list") }}');
        const data = await response.json();
        if (data.success) {
            data.chats.forEach(chat => {
                const chatElement = document.querySelector(`a[href*="chat_id=${chat.id}"]`);
                if (chatElement) {
                    const badge = chatElement.querySelector('.bg-red-500');
                    if (chat.unread_count > 0) {
                        if (!badge) {
                            const badgeHTML = `<span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0 ml-1 animate-pulse">${chat.unread_count}</span>`;
                            chatElement.querySelector('.flex.items-center.justify-between').insertAdjacentHTML('beforeend', badgeHTML);
                        } else {
                            badge.textContent = chat.unread_count;
                        }
                    } else if (badge) { badge.remove(); }
                }
            });
        }
    } catch (error) { console.error('Error polling chat list:', error); }
}

scrollToBottom();
setInterval(pollMessages, 3000);
setInterval(pollChatList, 5000);
</script>
@endif
@endsection
