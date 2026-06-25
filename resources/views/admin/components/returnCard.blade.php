<div class="glass-card rounded-2xl overflow-hidden group hover:border-white/20 transition-colors">
    {{-- Card Header --}}
    <div class="px-6 py-4 flex items-center justify-between border-b border-white/5
        @php
            $headerBg = [
                'pending'   => 'bg-yellow-500/5',
                'disetujui' => 'bg-emerald-500/5',
                'ditolak'   => 'bg-red-500/5',
                'diproses'  => 'bg-blue-500/5',
                'selesai'   => 'bg-slate-500/5',
            ];
            echo $headerBg[strtolower($return->status)] ?? 'bg-slate-800/50';
        @endphp
    ">
        <div>
            <h3 class="font-bold text-white text-base font-['Plus_Jakarta_Sans'] flex items-center gap-2">
                <i class="ri-arrow-go-back-line text-amber-500"></i>
                Retur #{{ $return->id }}
            </h3>
            <span class="text-xs text-slate-500 mt-0.5 block">{{ $return->created_at->format('d M Y, H:i') }}</span>
        </div>
        @php
            $statusConfig = [
                'pending'   => ['class' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20', 'icon' => 'ri-time-line'],
                'disetujui' => ['class' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20', 'icon' => 'ri-checkbox-circle-line'],
                'ditolak'   => ['class' => 'bg-red-500/10 text-red-400 border-red-500/20', 'icon' => 'ri-close-circle-line'],
                'diproses'  => ['class' => 'bg-blue-500/10 text-blue-400 border-blue-500/20', 'icon' => 'ri-loader-4-line'],
                'selesai'   => ['class' => 'bg-slate-500/10 text-slate-400 border-slate-500/20', 'icon' => 'ri-check-double-line'],
            ];
            $cfg = $statusConfig[strtolower($return->status)] ?? ['class' => 'bg-slate-700 text-slate-300 border-white/10', 'icon' => 'ri-question-line'];
        @endphp
        <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border {{ $cfg['class'] }}">
            <i class="{{ $cfg['icon'] }}"></i> {{ $return->status }}
        </span>
    </div>

    <div class="p-6">
        {{-- Customer & Order Info --}}
        <div class="flex flex-wrap gap-4 mb-5">
            <div class="flex items-center gap-2 text-sm">
                <span class="text-slate-500">Customer:</span>
                <span class="font-semibold text-slate-200">{{ $return->user->name }}</span>
                <span class="text-slate-500 text-xs">({{ $return->user->email }})</span>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <span class="text-slate-500">Order ID:</span>
                <span class="font-bold text-amber-400 font-mono">#{{ $return->order_id }}</span>
            </div>
        </div>

        {{-- Product Info --}}
        <div class="flex gap-4 p-4 bg-slate-900/50 border border-white/5 rounded-2xl mb-5">
            <div class="w-20 h-20 rounded-xl overflow-hidden border border-white/10 shrink-0 bg-slate-900">
                <img src="{{ $return->produk->image_url }}" 
                     alt="{{ $return->produk->nama_produk }}" 
                     class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-slate-200 mb-1">{{ $return->produk->nama_produk }}</h4>
                <p class="text-xs text-slate-400 mb-2 flex items-center gap-3">
                    <span><i class="ri-t-shirt-line"></i> {{ $return->orderItem->ukuran }}</span>
                    <span><i class="ri-stack-line"></i> Qty: {{ $return->orderItem->qty }}</span>
                </p>
                <p class="text-sm font-bold text-amber-400">Rp {{ number_format($return->orderItem->harga, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Return Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm mb-5">
            <div class="flex items-start gap-2 p-3 bg-slate-800/30 rounded-xl">
                <span class="text-slate-500 shrink-0 w-28">Alasan:</span>
                <span class="text-slate-300 font-medium">{{ $return->reason }}</span>
            </div>
            <div class="flex items-start gap-2 p-3 bg-slate-800/30 rounded-xl">
                <span class="text-slate-500 shrink-0 w-28">Tipe:</span>
                <span class="text-slate-300 font-bold">{{ $return->action_type }}</span>
            </div>
            @if($return->description)
            <div class="flex items-start gap-2 p-3 bg-slate-800/30 rounded-xl md:col-span-2">
                <span class="text-slate-500 shrink-0 w-28">Keterangan:</span>
                <span class="text-slate-300">{{ $return->description }}</span>
            </div>
            @endif
            <div class="flex items-center gap-2 p-3 bg-amber-500/5 border border-amber-500/10 rounded-xl">
                <span class="text-slate-500 shrink-0 w-28">Refund:</span>
                <span class="text-amber-400 font-bold text-base">Rp {{ number_format($return->refund_amount, 0, ',', '.') }}</span>
            </div>
            @if($return->photo_proof)
            <div class="flex items-center gap-2 p-3 bg-slate-800/30 rounded-xl">
                <span class="text-slate-500 shrink-0 w-28">Foto Bukti:</span>
                <a href="{{ asset('storage/' . $return->photo_proof) }}" target="_blank" 
                   class="flex items-center gap-1.5 text-blue-400 hover:text-blue-300 font-semibold transition-colors">
                    <i class="ri-image-line"></i> Lihat Foto
                </a>
            </div>
            @endif
            @if($return->courier_photo)
            <div class="flex items-center gap-2 p-3 bg-emerald-500/5 border border-emerald-500/10 rounded-xl">
                <span class="text-slate-500 shrink-0 w-28">Bukti Kurir:</span>
                <a href="{{ asset('storage/' . $return->courier_photo) }}" target="_blank"
                   class="flex items-center gap-1.5 text-emerald-400 hover:text-emerald-300 font-semibold transition-colors">
                    <i class="ri-image-line"></i> Lihat Bukti
                </a>
            </div>
            @elseif($return->status === 'Disetujui')
            <div class="flex items-center gap-2 p-3 bg-yellow-500/5 border border-yellow-500/10 rounded-xl">
                <span class="text-slate-500 shrink-0 w-28">Bukti Kurir:</span>
                <span class="text-yellow-300 font-semibold">Menunggu customer upload</span>
            </div>
            @endif
        </div>

        {{-- Admin Note --}}
        @if($return->admin_note)
            <div class="bg-blue-500/5 border border-blue-500/10 border-l-4 border-l-blue-400 p-4 mb-5 rounded-xl">
                <p class="font-bold text-slate-300 mb-1 flex items-center gap-2"><i class="ri-chat-quote-line text-blue-400"></i> Catatan Admin:</p>
                <p class="text-sm text-slate-400">{{ $return->admin_note }}</p>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap gap-2 pt-4 border-t border-white/5">
            @if($return->status === 'Pending')
                <button class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl transition-all text-sm hover:scale-[1.02] hover:shadow-lg hover:shadow-emerald-500/20"
                        onclick="updateStatus({{ $return->id }}, 'Disetujui')">
                    <i class="ri-checkbox-circle-line"></i> Setujui
                </button>
                <button class="flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-xl transition-all text-sm hover:scale-[1.02] hover:shadow-lg hover:shadow-red-500/20"
                        onclick="promptReject({{ $return->id }})">
                    <i class="ri-close-circle-line"></i> Tolak
                </button>
            @elseif($return->status === 'Disetujui')
                @if($return->courier_photo)
                    <button class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all text-sm hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/20"
                            onclick="updateStatus({{ $return->id }}, 'Diproses')">
                        <i class="ri-loader-4-line"></i> Proses Refund
                    </button>
                @else
                    <button class="flex items-center gap-2 px-5 py-2.5 bg-white/5 text-slate-500 font-semibold rounded-xl border border-white/5 text-sm cursor-not-allowed" disabled>
                        <i class="ri-time-line"></i> Menunggu Bukti Kurir
                    </button>
                @endif
            @elseif($return->status === 'Diproses')
                <button class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl transition-all text-sm hover:scale-[1.02]"
                        onclick="updateStatus({{ $return->id }}, 'Selesai')">
                    <i class="ri-check-double-line"></i> Selesaikan
                </button>
            @endif

            <form action="{{ route('admin.returns.destroy', $return->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="flex items-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-red-500/10 text-slate-400 hover:text-red-400 font-semibold rounded-xl border border-white/5 hover:border-red-500/20 transition-all text-sm"
                        onclick="return confirm('Yakin ingin menghapus data retur ini?')">
                    <i class="ri-delete-bin-line"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function updateStatus(returnId, status) {
    fetch(`/admin/returns/${returnId}/update-status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ status: status, admin_note: '' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Status retur gagal diupdate', 'error');
        }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
}

function promptReject(returnId) {
    const reason = prompt('Alasan penolakan:');
    if (reason === null) return;
    if (!reason.trim()) { alert('Alasan penolakan harus diisi!'); return; }

    fetch(`/admin/returns/${returnId}/update-status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ status: 'Ditolak', admin_note: reason })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 1000); }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
}
</script>
