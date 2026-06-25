@props(['message'])

{{-- Tentukan class berdasarkan pengirim (user: kiri, admin: kanan) --}}
<div class="chat-bubble-wrapper bubble-{{ $message['sender'] }}">
    <div class="chat-bubble">
        <span class="message-text">{{ $message['message'] }}</span>
    </div>
    @if ($message['sender'] == 'admin')
        {{-- Icon Admin/Staff --}}
        <span class="sender-icon">👩‍💼</span>
    @else
        {{-- Icon User/Pelanggan --}}
        <span class="sender-icon">👤</span>
    @endif
</div>