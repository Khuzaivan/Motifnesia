@props(['chat'])

<div class="chat-list-item">
    <div class="chat-user-info">
        <span class="chat-user-icon">👤</span>
        <div class="chat-text-preview">
            <p class="chat-username">{{ $chat['user_name'] }}</p>
            <p class="chat-preview-text">{{ $chat['last_message'] }}</p>
        </div>
    </div>
    <span class="chat-icon">💬</span>
</div>