@extends('admin.layouts.mainLayout')

@section('title', 'Broadcast Promo Member')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card rounded-2xl p-5">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <i class="ri-megaphone-line text-amber-500"></i> Broadcast Promo Member
        </h2>
        <p class="text-slate-400 text-sm mt-1">Kirim promo member lewat email otomatis. Notifikasi internal tetap dibuat sebagai arsip di akun customer.</p>
    </div>

    @if(in_array(config('mail.default'), ['log', 'array'], true))
        <div class="bg-amber-500/10 border border-amber-500/20 text-amber-700 p-4 rounded-xl font-semibold">
            Mailer masih mode {{ config('mail.default') }}. Email akan tercatat di log Laravel, belum terkirim ke inbox asli.
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-4 rounded-xl">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.membership-broadcast.store') }}" method="POST" class="glass-card rounded-3xl p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-bold text-slate-300 mb-2">Judul Promo</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500" placeholder="Contoh: Promo Khusus Member Akhir Pekan">
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-300 mb-2">Pesan Promo</label>
            <textarea name="message" rows="5" required class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500" placeholder="Tulis pesan promo untuk member...">{{ old('message', $broadcastMessage) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-300 mb-2">Keterangan Email</label>
            <textarea name="caption" rows="3" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500" placeholder="Contoh: Voucher hanya berlaku untuk pembelian batik tertentu sampai akhir bulan.">{{ old('caption', $broadcastCaption ?? '') }}</textarea>
            <p class="text-slate-500 text-xs mt-2">Keterangan ini akan masuk ke bagian caption/deskripsi email otomatis.</p>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition-all">
            <i class="ri-mail-send-line"></i> Kirim Email Member
        </button>
    </form>

    <div class="glass-card rounded-3xl overflow-hidden">
        <div class="p-5 border-b border-white/10 flex items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-white">Member Aktif</h3>
                <p class="text-slate-400 text-sm">Email promo akan dikirim otomatis ke member yang punya alamat email.</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-white/5 text-slate-300 text-xs font-bold">{{ $members->total() }} member</span>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 font-bold">Member</th>
                        <th class="px-6 py-4 font-bold">Email</th>
                        <th class="px-6 py-4 text-center font-bold">Status Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($members as $member)
                        <tr class="hover:bg-slate-700/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">{{ $member->full_name ?: $member->name }}</div>
                                <div class="text-xs text-slate-500">Bergabung {{ optional($member->membership_joined_at)->format('d M Y') ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $member->email ?: '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($member->email)
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold">
                                        <i class="ri-mail-check-line"></i> Siap Dikirim
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-500/10 text-slate-400 border border-slate-500/20 text-xs font-bold">
                                        <i class="ri-mail-forbid-line"></i> Email Belum Ada
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center text-slate-500">Belum ada member aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($members->hasPages())
        <div class="flex justify-center">{{ $members->links() }}</div>
    @endif
</div>
@endsection
