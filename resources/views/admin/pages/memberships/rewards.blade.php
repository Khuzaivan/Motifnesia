@extends('admin.layouts.mainLayout')

@section('title', 'Reward Membership')

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
                <i class="ri-gift-line text-amber-500"></i> Reward / Voucher Membership
            </h2>
            <p class="text-slate-400 text-sm mt-1">Kelola voucher yang bisa ditukar customer dengan poin.</p>
        </div>
        <a href="{{ route('admin.membership-rewards.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition-all text-sm font-bold">
            <i class="ri-add-line"></i> Tambah Reward
        </a>
    </div>

    <div class="glass-card rounded-3xl overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 font-bold">Reward</th>
                        <th class="px-6 py-4 font-bold">Diskon</th>
                        <th class="px-6 py-4 text-center font-bold">Poin</th>
                        <th class="px-6 py-4 text-center font-bold">Ditukar</th>
                        <th class="px-6 py-4 text-center font-bold">Status</th>
                        <th class="px-6 py-4 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($rewards as $reward)
                        <tr class="hover:bg-slate-700/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">{{ $reward->title }}</div>
                                <div class="text-xs text-slate-500 mt-1 max-w-md">{{ $reward->description ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-amber-400 font-bold">{{ $reward->discount_label }}</td>
                            <td class="px-6 py-4 text-center font-bold">{{ number_format($reward->points_required, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">{{ $reward->redemptions_count }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $reward->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                    {{ $reward->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.membership-rewards.edit', $reward->id) }}" class="w-8 h-8 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-all" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    @if($reward->is_active)
                                        <form action="{{ route('admin.membership-rewards.destroy', $reward->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan reward ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="w-8 h-8 rounded-lg bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white flex items-center justify-center transition-all" title="Nonaktifkan">
                                                <i class="ri-close-circle-line"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-500">
                                <i class="ri-gift-line text-5xl block mb-3 text-slate-600"></i>
                                Belum ada reward membership.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($rewards->hasPages())
        <div class="flex justify-center">{{ $rewards->links() }}</div>
    @endif
</div>
@endsection
