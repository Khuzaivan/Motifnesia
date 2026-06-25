@extends('admin.layouts.mainLayout')

@section('title', 'Supplier')

@section('content')
@php
    $statusClasses = [
        'active' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
        'inactive' => 'bg-slate-500/10 text-slate-300 border-slate-500/20',
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 p-4 rounded-xl font-semibold motion-pop">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-4 rounded-xl font-semibold motion-pop">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-4 rounded-xl motion-pop">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-[420px_1fr] gap-6">
        <form action="{{ route('admin.suppliers.store') }}" method="POST" class="glass-card rounded-3xl p-6 space-y-4 supply-tilt" data-aos="fade-right">
            @csrf
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-amber-300 font-bold">Master Supplier</p>
                <h2 class="text-2xl font-extrabold text-white mt-1">Tambah Supplier</h2>
                <p class="text-sm text-slate-400 mt-1">Supplier bisa diberi akun login untuk memproses pengadaan stok.</p>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-bold text-slate-300 mb-2">Nama Supplier</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" placeholder="Contoh: Batik Nusantara Supplier">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-300 mb-2">Kontak Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" placeholder="Nama PIC supplier">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" placeholder="supplier@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-300 mb-2">Password Login Supplier</label>
                    <input type="password" name="password" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" placeholder="Opsional, minimal 6 karakter">
                    <p class="text-xs text-slate-500 mt-2">Isi password kalau supplier perlu login ke portal supplier.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-300 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none">
                        <option value="active" @selected(old('status', 'active') === 'active')>Aktif</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-300 mb-2">Alamat</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" placeholder="Alamat supplier">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-300 mb-2">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" placeholder="Catatan kerja sama, lead time, atau kualitas barang">{{ old('notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn-magnetic w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold transition-all">
                <i class="ri-save-3-line"></i> Simpan Supplier
            </button>
        </form>

        <div class="glass-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-left">
            <div class="p-6 border-b border-white/10 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-amber-300 font-bold">Daftar Mitra</p>
                    <h2 class="text-2xl font-extrabold text-white mt-1">Supplier Aktif</h2>
                </div>
                <span class="inline-flex w-max items-center gap-2 px-3 py-2 rounded-xl bg-white/5 text-slate-300 border border-white/10 text-sm font-bold">
                    <i class="ri-building-4-line text-amber-400"></i> {{ $suppliers->total() }} supplier
                </span>
            </div>

            <div class="divide-y divide-white/5">
                @forelse($suppliers as $supplier)
                    <div class="p-5 hover:bg-white/[0.03] transition-colors" data-supply-animate>
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-extrabold text-white">{{ $supplier->name }}</h3>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border text-xs font-bold {{ $statusClasses[$supplier->status] ?? $statusClasses['inactive'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span> {{ $supplier->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-400 mt-1">{{ $supplier->contact_person ?: 'PIC belum diisi' }} | {{ $supplier->email ?: 'email belum ada' }} | {{ $supplier->phone ?: 'telepon belum ada' }}</p>
                                <p class="text-sm text-slate-500 mt-2">{{ $supplier->address ?: 'Alamat belum diisi.' }}</p>
                                <p class="text-xs text-slate-500 mt-2">{{ $supplier->procurements_count }} pengadaan | Akun login: {{ $supplier->user ? 'tersambung' : 'belum dibuat' }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($supplier->status === 'active')
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Nonaktifkan supplier ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-300 hover:text-white border border-red-500/20 font-bold text-sm transition-all">
                                            <i class="ri-close-circle-line"></i> Nonaktif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <details class="mt-4">
                            <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 font-bold text-sm transition-all">
                                        <i class="ri-edit-line text-amber-400"></i> Edit
                            </summary>
                            <div class="mt-4 rounded-2xl bg-slate-950/50 border border-white/10 p-4">
                                <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <input type="text" name="name" value="{{ $supplier->name }}" required class="px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white" placeholder="Nama supplier">
                                        <input type="text" name="contact_person" value="{{ $supplier->contact_person }}" class="px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white" placeholder="PIC">
                                        <input type="email" name="email" value="{{ $supplier->email }}" class="px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white" placeholder="Email">
                                        <input type="text" name="phone" value="{{ $supplier->phone }}" class="px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white" placeholder="Telepon">
                                        <select name="status" class="px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white">
                                            <option value="active" @selected($supplier->status === 'active')>Aktif</option>
                                            <option value="inactive" @selected($supplier->status === 'inactive')>Nonaktif</option>
                                        </select>
                                        <input type="password" name="password" class="px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white" placeholder="Password baru opsional">
                                    </div>
                                    <textarea name="address" rows="2" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white" placeholder="Alamat">{{ $supplier->address }}</textarea>
                                    <textarea name="notes" rows="2" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white" placeholder="Catatan">{{ $supplier->notes }}</textarea>
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold">
                                        <i class="ri-save-3-line"></i> Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                        </details>
                    </div>
                @empty
                    <div class="p-16 text-center text-slate-500">Belum ada supplier.</div>
                @endforelse
            </div>
        </div>
    </div>

    @if($suppliers->hasPages())
        <div class="flex justify-center">{{ $suppliers->links() }}</div>
    @endif
</div>
@endsection
