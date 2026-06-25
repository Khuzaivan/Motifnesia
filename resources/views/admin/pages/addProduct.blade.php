@extends('admin.layouts.mainLayout')

@section('title', $formTitle)

@section('content')
    <style>
        .admin-product-page .admin-product-back-link {
            color: #94a3b8;
        }

        .admin-product-page .admin-product-back-link:hover {
            color: #f59e0b;
        }

        .admin-product-form-card .bg-white {
            background-color: #111827;
            border-color: rgba(148, 163, 184, 0.16);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .admin-product-form-card .border-gray-200,
        .admin-product-form-card .border-gray-300 {
            border-color: rgba(148, 163, 184, 0.22);
        }

        .admin-product-form-card .text-gray-800,
        .admin-product-form-card .text-gray-700 {
            color: #e2e8f0;
        }

        .admin-product-form-card .text-gray-500 {
            color: #94a3b8;
        }

        .admin-product-form-card input:not([type="checkbox"]):not([type="radio"]),
        .admin-product-form-card select,
        .admin-product-form-card textarea {
            background-color: #0f172a;
            border-color: rgba(148, 163, 184, 0.26);
            color: #e2e8f0;
            caret-color: #f59e0b;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
        }

        .admin-product-form-card input:focus,
        .admin-product-form-card select:focus,
        .admin-product-form-card textarea:focus {
            border-color: #f59e0b;
            background-color: #111827;
        }

        .admin-product-form-card input::placeholder,
        .admin-product-form-card textarea::placeholder {
            color: #64748b;
            opacity: 1;
        }

        .admin-product-form-card select {
            color-scheme: dark;
        }

        .admin-product-form-card select option {
            background-color: #111827;
            color: #e2e8f0;
        }

        .admin-product-form-card select option[value=""] {
            color: #94a3b8;
        }

        .admin-product-form-card input[readonly] {
            background-color: rgba(22, 101, 52, 0.16);
            border-color: rgba(74, 222, 128, 0.22);
            color: #86efac;
        }

        .admin-product-form-card input[type="file"] {
            color: #cbd5e1;
        }

        .admin-product-form-card input[type="file"]::file-selector-button {
            margin-right: 12px;
            border: 1px solid rgba(148, 163, 184, 0.24);
            border-radius: 8px;
            background: #1e293b;
            color: #e2e8f0;
            padding: 7px 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .admin-product-form-card img#productPreview {
            background-color: #0f172a;
            border-color: rgba(148, 163, 184, 0.22);
        }

        .admin-product-form-card .bg-amber-50 {
            background-color: rgba(245, 158, 11, 0.08);
            border-color: rgba(245, 158, 11, 0.28);
        }

        .admin-product-form-card input[type="checkbox"] {
            accent-color: #d97706;
        }

        .light-mode .admin-product-page .admin-product-back-link {
            color: #475569;
        }

        .light-mode .admin-product-page .admin-product-back-link:hover {
            color: #d97706;
        }

        .light-mode .admin-product-form-card .bg-white {
            background-color: #ffffff;
            border-color: #e5e7eb;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        }

        .light-mode .admin-product-form-card .border-gray-200 {
            border-color: #e5e7eb;
        }

        .light-mode .admin-product-form-card .border-gray-300 {
            border-color: #d1d5db;
        }

        .light-mode .admin-product-form-card .text-gray-800 {
            color: #1f2937;
        }

        .light-mode .admin-product-form-card .text-gray-700 {
            color: #374151;
        }

        .light-mode .admin-product-form-card .text-gray-500 {
            color: #6b7280;
        }

        .light-mode .admin-product-form-card input:not([type="checkbox"]):not([type="radio"]),
        .light-mode .admin-product-form-card select,
        .light-mode .admin-product-form-card textarea {
            background-color: #ffffff;
            border-color: #d1d5db;
            color: #0f172a;
            caret-color: #0f172a;
            box-shadow: none;
        }

        .light-mode .admin-product-form-card input::placeholder,
        .light-mode .admin-product-form-card textarea::placeholder {
            color: #94a3b8;
        }

        .light-mode .admin-product-form-card select {
            color-scheme: light;
        }

        .light-mode .admin-product-form-card select option {
            background-color: #ffffff;
            color: #0f172a;
        }

        .light-mode .admin-product-form-card select option[value=""] {
            color: #64748b;
        }

        .light-mode .admin-product-form-card input[readonly] {
            background-color: #f0fdf4;
            border-color: #d1d5db;
            color: #15803d;
        }

        .light-mode .admin-product-form-card input[type="file"] {
            color: #334155;
        }

        .light-mode .admin-product-form-card input[type="file"]::file-selector-button {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: #334155;
        }

        .light-mode .admin-product-form-card img#productPreview {
            background-color: #ffffff;
            border-color: #e5e7eb;
        }

        .light-mode .admin-product-form-card .bg-amber-50 {
            background-color: #fffbeb;
            border-color: #fde68a;
        }
    </style>

    <div class="admin-product-page p-6 max-w-7xl">
        {{-- Header with Back Button --}}
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('admin.product.management.index') }}" 
               class="admin-product-back-link flex items-center gap-2 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Manajemen Produk
            </a>
        </div>

        {{-- Display Success Message --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <div class="font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        {{-- Display Error Message --}}
        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="font-semibold mb-2">Terdapat kesalahan dalam pengisian form:</div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.create') }}" method="POST" enctype="multipart/form-data" class="admin-product-form-card">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Form Fields --}}
                <div class="lg:col-span-2 glass-card rounded-2xl p-6">
                    <div class="space-y-4">
                    
                        {{-- Nama Produk --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $product['name']) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Harga --}}
                            <div>
                                <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Harga Asli <span class="text-red-500">*</span></label>
                                <input type="number" id="price" name="price" value="{{ old('price', $product['price']) }}" step="0.01" min="0" required
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('price') border-red-500 @enderror">
                                @error('price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Stok --}}
                            <div>
                                <label for="stock" class="block text-sm font-semibold text-gray-700 mb-2">Total Stok</label>
                                <input type="number" id="stock" name="stock" value="{{ old('stock', $product['stock']) }}" min="0" required readonly
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('stock') border-red-500 @enderror">
                                <p class="text-xs text-gray-500 mt-1">Terisi otomatis dari stok per ukuran.</p>
                                @error('stock')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Diskon & Total Harga --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Diskon --}}
                            <div>
                                <label for="diskon_persen" class="block text-sm font-semibold text-gray-700 mb-2">Diskon (%)</label>
                                <input type="number" id="diskon_persen" name="diskon_persen" value="{{ old('diskon_persen', 0) }}" step="1" min="0" max="100"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                                       placeholder="Contoh: 10">
                            </div>

                            {{-- Total Harga (Read Only) --}}
                            <div>
                                <label for="total_harga_display" class="block text-sm font-semibold text-gray-700 mb-2">Total Harga Setelah Diskon</label>
                                <input type="text" id="total_harga_display" readonly
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-green-50 text-green-700 font-bold focus:outline-none"
                                       placeholder="Rp 0">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Material --}}
                            <div>
                                <label for="material" class="block text-sm font-semibold text-gray-700 mb-2">Material</label>
                                <select id="material" name="material"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">-- Pilih Material --</option>
                                    <option value="Katun" {{ old('material', $product['material']) == 'Katun' ? 'selected' : '' }}>Katun</option>
                                    <option value="Sutra" {{ old('material', $product['material']) == 'Sutra' ? 'selected' : '' }}>Sutra</option>
                                </select>
                            </div>

                            {{-- Proses --}}
                            <div>
                                <label for="process" class="block text-sm font-semibold text-gray-700 mb-2">Proses</label>
                                <select id="process" name="process"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">-- Pilih Proses --</option>
                                    <option value="Press" {{ old('process', $product['process']) == 'Press' ? 'selected' : '' }}>Press</option>
                                    <option value="Tulis" {{ old('process', $product['process']) == 'Tulis' ? 'selected' : '' }}>Tulis</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- SKU --}}
                            <div>
                                <label for="sku" class="block text-sm font-semibold text-gray-700 mb-2">SKU</label>
                                <input type="text" id="sku" name="sku" value="{{ old('sku', $product['sku']) }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>

                            {{-- Kategori --}}
                            <div>
                                <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                                <select id="category" name="category" required
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('category') border-red-500 @enderror">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Pria" {{ old('category', $product['category']) == 'Pria' ? 'selected' : '' }}>Pria</option>
                                    <option value="Wanita" {{ old('category', $product['category']) == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                                    <option value="Anak-anak" {{ old('category', $product['category']) == 'Anak-anak' ? 'selected' : '' }}>Anak-anak</option>
                                </select>
                                @error('category')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Ukuran --}}
                            <div>
                                <label for="ukuran" class="block text-sm font-semibold text-gray-700 mb-2">Ukuran Utama</label>
                                <select id="ukuran" name="ukuran"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">-- Pilih Ukuran --</option>
                                    <option value="S" {{ old('ukuran', $product['ukuran']) == 'S' ? 'selected' : '' }}>S</option>
                                    <option value="M" {{ old('ukuran', $product['ukuran']) == 'M' ? 'selected' : '' }}>M</option>
                                    <option value="L" {{ old('ukuran', $product['ukuran']) == 'L' ? 'selected' : '' }}>L</option>
                                    <option value="XL" {{ old('ukuran', $product['ukuran']) == 'XL' ? 'selected' : '' }}>XL</option>
                                </select>
                            </div>

                            {{-- Jenis Lengan --}}
                            <div>
                                <label for="jenis_lengan" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Lengan</label>
                                <select id="jenis_lengan" name="jenis_lengan"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">-- Pilih Jenis Lengan --</option>
                                    <option value="Pendek" {{ old('jenis_lengan', $product['jenis_lengan']) == 'Pendek' ? 'selected' : '' }}>Pendek</option>
                                    <option value="Panjang" {{ old('jenis_lengan', $product['jenis_lengan']) == 'Panjang' ? 'selected' : '' }}>Panjang</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Stok Per Ukuran</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach(['S','M','L','XL'] as $size)
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">{{ $size }}</label>
                                        <input type="number" name="size_stocks[{{ $size }}]" value="{{ old('size_stocks.' . $size, 0) }}" min="0"
                                               class="size-stock-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Sistem akan validasi keranjang dan checkout berdasarkan ukuran yang dipilih customer.</p>
                        </div>

                        {{-- Tags --}}
                        <div>
                            <label for="tags" class="block text-sm font-semibold text-gray-700 mb-2">Tags</label>
                            <input type="text" id="tags" name="tags" value="{{ old('tags', $product['tags']) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                                   placeholder="Contoh: batik, modern, casual">
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Produk <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="4" required
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('description') border-red-500 @enderror">{{ old('description', $product['description']) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Filosofi Motif --}}
                        <div>
                            <label for="filosofi_motif" class="block text-sm font-semibold text-gray-700 mb-2">Filosofi & Cerita Motif</label>
                            <textarea id="filosofi_motif" name="filosofi_motif" rows="4"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('filosofi_motif') border-red-500 @enderror"
                                      placeholder="Ceritakan filosofi atau kisah di balik motif batik ini (opsional)...">{{ old('filosofi_motif', $product['filosofi_motif'] ?? '') }}</textarea>
                            @error('filosofi_motif')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                {{-- Right Column: Image Upload --}}
                <div class="lg:col-span-1">
                    <div class="glass-card rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Foto Produk</h3>
                        <div class="mb-4">
                            <img src="{{ $product['image'] }}" alt="Pratinjau Produk" 
                                 class="w-full h-64 object-cover rounded-lg border-2 border-gray-200" id="productPreview">
                        </div>
                        <input type="file" name="image" id="elect-image-button" accept="image/*"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG. Max: 2MB</p>

                        <label class="mt-5 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 cursor-pointer">
                            <input type="checkbox" name="notify_members" value="1" class="mt-1 h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500" {{ old('notify_members') ? 'checked' : '' }}>
                            <span>
                                <span class="block text-sm font-semibold text-gray-800">Kirim info ke member</span>
                                <span class="block text-xs text-gray-500 mt-1">Member aktif akan menerima notifikasi internal tentang produk baru ini.</span>
                            </span>
                        </label>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                    class="btn-magnetic w-full flex items-center justify-center gap-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-semibold px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Produk
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const fileInput = document.getElementById('elect-image-button');
            const preview = document.getElementById('productPreview');
            const priceInput = document.getElementById('price');
            const diskonInput = document.getElementById('diskon_persen');
            const totalHargaDisplay = document.getElementById('total_harga_display');
            const stockInput = document.getElementById('stock');
            const sizeStockInputs = document.querySelectorAll('.size-stock-input');

            // Handle image preview
            if (fileInput) {
                fileInput.addEventListener('change', function(e){
                    const file = e.target.files && e.target.files[0];
                    if (!file) return;
                    const url = URL.createObjectURL(file);
                    preview.src = url;
                    preview.onload = function(){
                        URL.revokeObjectURL(url);
                    }
                });
            }

            // Kalkulasi total harga setelah diskon
            function calculateTotalHarga() {
                const harga = parseFloat(priceInput.value) || 0;
                const diskon = parseFloat(diskonInput.value) || 0;
                
                // Validasi diskon
                if (diskon < 0) diskonInput.value = 0;
                if (diskon > 100) diskonInput.value = 100;
                
                const finalDiskon = parseFloat(diskonInput.value) || 0;
                const totalHarga = harga - (harga * (finalDiskon / 100));
                
                // Format currency
                totalHargaDisplay.value = 'Rp ' + totalHarga.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            // Event listeners untuk kalkulasi otomatis
            if (priceInput && diskonInput && totalHargaDisplay) {
                priceInput.addEventListener('input', calculateTotalHarga);
                diskonInput.addEventListener('input', calculateTotalHarga);
                
                // Initial calculation
                calculateTotalHarga();
            }

            function updateTotalStock() {
                let total = 0;
                sizeStockInputs.forEach(input => total += parseInt(input.value || '0', 10));
                if (stockInput) stockInput.value = total;
            }

            sizeStockInputs.forEach(input => input.addEventListener('input', updateTotalStock));
            updateTotalStock();
        });
    </script>
    
@endsection
