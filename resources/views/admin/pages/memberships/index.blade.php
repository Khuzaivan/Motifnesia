@extends('admin.layouts.mainLayout')

@section('title', 'Daftar Member')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card rounded-2xl p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ri-vip-crown-line text-amber-500"></i> Daftar Member
            </h2>
            <p class="text-slate-400 text-sm mt-1">Customer yang sudah mengaktifkan membership Motifnesia.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.membership-rewards.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500 hover:text-white transition-all text-sm font-bold">
                <i class="ri-gift-line"></i> Kelola Reward
            </a>
            <a href="{{ route('admin.membership-broadcast.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500 hover:text-white transition-all text-sm font-bold">
                <i class="ri-megaphone-line"></i> Broadcast Promo
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-5">
            <p class="text-slate-400 text-sm">Total Member</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ number_format($members->total(), 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card rounded-2xl p-5">
            <p class="text-slate-400 text-sm">Member Aktif di Halaman Ini</p>
            <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ $members->getCollection()->where('membership_status', 'active')->count() }}</h3>
        </div>
        <div class="glass-card rounded-2xl p-5">
            <p class="text-slate-400 text-sm">Total Poin di Halaman Ini</p>
            <h3 class="text-2xl font-bold text-amber-400 mt-1">{{ number_format($members->getCollection()->sum('reward_points'), 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="glass-card rounded-3xl overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 font-bold">Member</th>
                        <th class="px-6 py-4 font-bold">Kontak</th>
                        <th class="px-6 py-4 text-center font-bold">Status</th>
                        <th class="px-6 py-4 text-center font-bold">Poin</th>
                        <th class="px-6 py-4 text-center font-bold">Voucher</th>
                        <th class="px-6 py-4 font-bold">Bergabung</th>
                        <th class="px-6 py-4 text-center font-bold">Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($members as $member)
                        <tr class="hover:bg-slate-700/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">{{ $member->full_name ?: $member->name }}</div>
                                <div class="text-xs text-slate-500">@ {{ $member->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-slate-300"><i class="ri-mail-line text-slate-500"></i> {{ $member->email }}</div>
                                <div class="flex items-center gap-2 text-slate-400 mt-1"><i class="ri-phone-line text-emerald-400"></i> {{ $member->phone_number ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $member->membership_status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                    {{ ucfirst($member->membership_status ?: 'inactive') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-amber-400">{{ number_format((int) $member->reward_points, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-lg bg-white/5 text-slate-300 text-xs font-bold">{{ $member->reward_redemptions_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ optional($member->membership_joined_at)->format('d M Y') ?: '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-3 min-w-[260px]">
                                    <form action="{{ route('admin.memberships.status', $member->id) }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <select name="membership_status" class="flex-1 px-3 py-2 bg-slate-900 border border-white/10 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                                            <option value="active" {{ $member->membership_status === 'active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="inactive" {{ $member->membership_status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                        <button type="submit" class="px-3 py-2 bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white border border-blue-500/20 rounded-xl text-xs font-bold transition-all">Simpan</button>
                                    </form>
                                    <form action="{{ route('admin.memberships.points', $member->id) }}" method="POST" class="grid grid-cols-[90px_1fr_auto] gap-2">
                                        @csrf
                                        <input type="number" name="points" placeholder="+/- poin" required class="px-3 py-2 bg-slate-900 border border-white/10 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                                        <input type="text" name="description" placeholder="Catatan" class="px-3 py-2 bg-slate-900 border border-white/10 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                                        <button type="submit" class="px-3 py-2 bg-amber-500/10 hover:bg-amber-500 text-amber-400 hover:text-white border border-amber-500/20 rounded-xl text-xs font-bold transition-all">Adjust</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                                <i class="ri-vip-crown-line text-5xl block mb-3 text-slate-600"></i>
                                Belum ada customer yang mendaftar membership.
                            </td>
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
