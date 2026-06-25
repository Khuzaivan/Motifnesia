{{-- Filter Sidebar - LuxeFurnish Premium Style --}}
<aside class="p-6">
    <form id="filterForm" method="GET" action="{{ route('customer.home') }}">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        @php
            $genderOptions = $filterOptions['genders'] ?? ['Pria', 'Wanita', 'Anak-anak'];
            $sleeveOptions = $filterOptions['sleeves'] ?? ['Pendek', 'Panjang'];
            $priceRanges = $filterOptions['price_ranges'] ?? [
                ['value' => '0-200000', 'label' => 'Di bawah Rp 200.000'],
                ['value' => '200000-400000', 'label' => 'Rp 200.000 - Rp 400.000'],
                ['value' => '400000-600000', 'label' => 'Rp 400.000 - Rp 600.000'],
                ['value' => '600000-800000', 'label' => 'Rp 600.000 - Rp 800.000'],
                ['value' => '800000-999999999', 'label' => 'Di atas Rp 800.000'],
            ];
        @endphp

        {{-- Header --}}
        <div class="flex items-center gap-2 mb-6 pb-4 border-b border-white/10">
            <div class="w-1 h-5 bg-[#c9a84c] rounded-full shadow-[0_0_8px_#c9a84c]"></div>
            <h3 class="text-base font-bold text-white/90" style="font-family:'Playfair Display',serif;">Filter Koleksi</h3>
        </div>

        {{-- Filter Kategori --}}
        <div class="mb-6">
            <h4 class="text-xs font-bold text-white/50 uppercase tracking-widest mb-4">Kategori</h4>

            <div class="space-y-4">
                <div>
                    <label for="gender" class="block text-sm font-semibold text-white/80 mb-2">Gender</label>
                    <div class="relative">
                        <select id="gender" name="gender"
                                class="w-full px-4 py-2.5 pr-10 bg-white/5 border border-white/10 rounded-xl text-sm text-white focus:ring-2 focus:ring-[#c9a84c]/40 focus:border-[#c9a84c] transition-all appearance-none cursor-pointer [&>option]:bg-[#181818] [&>option]:text-white">
                            <option value="">Semua</option>
                            @foreach($genderOptions as $gender)
                                <option value="{{ $gender }}" {{ strcasecmp((string) request('gender'), $gender) === 0 ? 'selected' : '' }}>{{ $gender }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-4 h-4 text-[#c9a84c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="jenis_lengan" class="block text-sm font-semibold text-white/80 mb-2">Jenis Lengan</label>
                    <div class="relative">
                        <select id="jenis_lengan" name="jenis_lengan"
                                class="w-full px-4 py-2.5 pr-10 bg-white/5 border border-white/10 rounded-xl text-sm text-white focus:ring-2 focus:ring-[#c9a84c]/40 focus:border-[#c9a84c] transition-all appearance-none cursor-pointer [&>option]:bg-[#181818] [&>option]:text-white">
                            <option value="">Semua</option>
                            @foreach($sleeveOptions as $sleeve)
                                <option value="{{ $sleeve }}" {{ strcasecmp((string) request('jenis_lengan'), $sleeve) === 0 ? 'selected' : '' }}>{{ $sleeve }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-4 h-4 text-[#c9a84c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="w-full mt-5 px-4 py-2.5 bg-gradient-to-r from-[#c9a84c] to-[#a8832d] hover:from-[#d4b45c] hover:to-[#b8933d] text-white font-bold rounded-xl transition-all duration-300 text-sm tracking-wide transform hover:scale-[1.02] shadow-[0_4px_15px_rgba(201,168,76,0.2)] hover:shadow-[0_4px_20px_rgba(201,168,76,0.4)]">
                Terapkan Filter
            </button>
        </div>

        {{-- Divider --}}
        <div class="border-t border-white/10 mb-6"></div>

        {{-- Filter Harga --}}
        <div class="mb-4">
            <h4 class="text-xs font-bold text-white/50 uppercase tracking-widest mb-4">Rentang Harga</h4>

            <div class="space-y-2">
                @foreach($priceRanges as $range)
                    <label class="flex items-center gap-3 cursor-pointer group p-2.5 rounded-xl hover:bg-white/5 transition-colors duration-200">
                        <div class="relative flex items-center justify-center">
                            <input type="radio" name="price_range" value="{{ $range['value'] }}"
                                   {{ request('price_range') == $range['value'] ? 'checked' : '' }}
                                   class="peer sr-only">
                            <div class="w-5 h-5 rounded-full border-2 border-white/20 group-hover:border-[#c9a84c] peer-checked:border-[#c9a84c] transition-colors duration-200 flex items-center justify-center">
                                <div class="w-2.5 h-2.5 rounded-full bg-[#c9a84c] opacity-0 peer-checked:opacity-100 transition-opacity duration-200 scale-0 peer-checked:scale-100 transform shadow-[0_0_5px_#c9a84c]"></div>
                            </div>
                        </div>
                        <span class="text-sm text-white/70 group-hover:text-[#c9a84c] transition-colors duration-200 font-medium">{{ $range['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Reset Filter --}}
        @if(request()->hasAny(['search', 'gender', 'jenis_lengan', 'price_range']))
            <div class="border-t border-white/10 mt-4 pt-4">
                <a href="{{ route('customer.home') }}"
                   class="flex items-center justify-center gap-2 w-full text-center px-4 py-2.5 bg-white/5 text-white/50 hover:text-white hover:bg-white/10 font-semibold rounded-xl transition-all duration-300 text-sm border border-transparent hover:border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </a>
            </div>
        @endif

    </form>
</aside>

<script>
document.querySelectorAll('input[name="price_range"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});
</script>
