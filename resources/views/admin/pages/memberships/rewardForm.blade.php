@extends('admin.layouts.mainLayout')

@section('title', $formTitle)

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="glass-card rounded-2xl p-5 flex items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ri-gift-line text-amber-500"></i> {{ $formTitle }}
            </h2>
            <p class="text-slate-400 text-sm mt-1">Atur poin dan jenis diskon untuk voucher member.</p>
        </div>
        <a href="{{ route('admin.membership-rewards.index') }}" class="px-4 py-2 rounded-xl bg-white/5 text-slate-300 hover:bg-white/10 transition-all text-sm font-bold">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-4 rounded-xl">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ $reward->exists ? route('admin.membership-rewards.update', $reward->id) : route('admin.membership-rewards.store') }}" method="POST" class="glass-card rounded-3xl p-6 space-y-5">
        @csrf
        @if($reward->exists)
            @method('PUT')
        @endif

        <div>
            <label class="block text-sm font-bold text-slate-300 mb-2">Judul Reward</label>
            <input type="text" name="title" value="{{ old('title', $reward->title) }}" required class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-300 mb-2">Deskripsi</label>
            <textarea name="description" rows="4" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500">{{ old('description', $reward->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-300 mb-2">Poin Dibutuhkan</label>
                <input type="number" name="points_required" min="1" value="{{ old('points_required', $reward->points_required ?: 1) }}" required class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-300 mb-2">Jenis Diskon</label>
                <select name="discount_type" required class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500">
                    @foreach(['fixed' => 'Potongan Rupiah', 'percent' => 'Persen', 'free_shipping' => 'Gratis Ongkir'] as $value => $label)
                        <option value="{{ $value }}" {{ old('discount_type', $reward->discount_type ?: 'fixed') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-300 mb-2">Nilai Diskon</label>
                <input type="number" name="discount_value" min="0" value="{{ old('discount_value', $reward->discount_value ?: 0) }}" required class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500">
            </div>
        </div>

        <div id="max_discount_wrapper" class="hidden">
            <label class="block text-sm font-bold text-slate-300 mb-2">Batas Maksimal Potongan Diskon (Khusus Persen)</label>
            <input type="number" name="max_discount_value" min="0" value="{{ old('max_discount_value', $reward->max_discount_value) }}" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500" placeholder="Kosongkan untuk tanpa batas">
            <p class="text-xs text-slate-400 mt-1">Batas maksimal nominal diskon dalam Rupiah (contoh: 50000 untuk maksimal diskon Rp 50.000).</p>
        </div>

        <label class="inline-flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-white/20 text-amber-500 focus:ring-amber-500" {{ old('is_active', $reward->exists ? $reward->is_active : true) ? 'checked' : '' }}>
            <span class="text-sm font-bold text-slate-300">Reward aktif dan bisa ditukar customer</span>
        </label>

        <div class="pt-4 border-t border-white/10 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition-all">
                <i class="ri-save-line"></i> Simpan Reward
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const discountTypeSelect = document.querySelector('select[name="discount_type"]');
        const maxDiscountWrapper = document.getElementById('max_discount_wrapper');
        const maxDiscountInput = document.querySelector('input[name="max_discount_value"]');

        function toggleMaxDiscount() {
            if (discountTypeSelect.value === 'percent') {
                maxDiscountWrapper.classList.remove('hidden');
            } else {
                maxDiscountWrapper.classList.add('hidden');
                maxDiscountInput.value = '';
            }
        }

        discountTypeSelect.addEventListener('change', toggleMaxDiscount);
        toggleMaxDiscount();
    });
</script>
@endpush
