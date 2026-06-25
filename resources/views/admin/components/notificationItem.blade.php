@php
    $isMembershipNotification = str_starts_with($notification->type, 'membership_') || in_array($notification->type, ['member_new_product', 'member_special_promo'], true);
@endphp

<div class="glass-card rounded-2xl p-5 flex flex-col sm:flex-row gap-4 group relative overflow-hidden transition-all hover:border-white/20 {{ !$notification->is_read ? 'bg-slate-800/80 border-l-4 border-l-amber-500' : 'bg-slate-800/30 opacity-75' }}">
    <!-- Icon -->
    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 border border-white/10
        @if($notification->type === 'order') bg-blue-500/10 text-blue-400
        @elseif($notification->type === 'stock') bg-red-500/10 text-red-400
        @elseif($notification->type === 'review') bg-amber-500/10 text-amber-400
        @elseif($notification->type === 'return') bg-orange-500/10 text-orange-400
        @elseif($isMembershipNotification) bg-amber-500/10 text-amber-400
        @else bg-slate-500/10 text-slate-400 @endif
    ">
        @if($notification->type === 'order') <i class="ri-shopping-cart-2-line text-xl"></i>
        @elseif($notification->type === 'stock') <i class="ri-box-3-line text-xl"></i>
        @elseif($notification->type === 'review') <i class="ri-star-line text-xl"></i>
        @elseif($notification->type === 'return') <i class="ri-arrow-go-back-line text-xl"></i>
        @elseif($isMembershipNotification) <i class="ri-vip-crown-line text-xl"></i>
        @else <i class="ri-notification-3-line text-xl"></i> @endif
    </div>

    <!-- Content -->
    <div class="flex-1">
        <div class="flex items-center gap-2 mb-1.5">
            <h3 class="font-bold text-white text-base">{{ $notification->title }}</h3>
            @if(!$notification->is_read)
                <span class="w-2 h-2 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.8)]"></span>
            @endif
            
            <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-md 
                @if($notification->priority === 'urgent') bg-red-500/20 text-red-400 border border-red-500/20
                @elseif($notification->priority === 'important') bg-amber-500/20 text-amber-400 border border-amber-500/20
                @elseif($notification->priority === 'info') bg-blue-500/20 text-blue-400 border border-blue-500/20
                @else bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 @endif
            ">
                {{ ucfirst($notification->priority) }}
            </span>
        </div>
        
        <p class="text-slate-300 text-sm mb-3">{{ $notification->message }}</p>
        
        <div class="flex items-center gap-4 text-xs text-slate-500">
            <span class="flex items-center gap-1.5"><i class="ri-time-line"></i> {{ $notification->time_ago }}</span>
            @if($notification->link)
                <a href="{{ $notification->link }}" class="text-amber-500 hover:text-amber-400 font-semibold transition-colors flex items-center gap-1">
                    Lihat Detail <i class="ri-arrow-right-line"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="flex sm:flex-col gap-2 justify-center sm:opacity-0 group-hover:opacity-100 transition-opacity absolute right-4 top-1/2 -translate-y-1/2 sm:static sm:translate-y-0">
        <button onclick="toggleRead({{ $notification->id }})" class="w-8 h-8 rounded-lg bg-slate-900 border border-white/10 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition-all" title="{{ $notification->is_read ? 'Tandai belum dibaca' : 'Tandai sudah dibaca' }}">
            @if($notification->is_read)
                <i class="ri-mail-unread-line"></i>
            @else
                <i class="ri-mail-check-line text-emerald-400"></i>
            @endif
        </button>
        <button onclick="deleteNotification({{ $notification->id }})" class="w-8 h-8 rounded-lg bg-slate-900 border border-white/10 text-slate-400 hover:text-red-400 hover:bg-red-500/10 hover:border-red-500/20 flex items-center justify-center transition-all" title="Hapus notifikasi">
            <i class="ri-delete-bin-line"></i>
        </button>
    </div>
</div>
