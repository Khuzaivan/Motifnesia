<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Produk;
use App\Models\KontenSlideShow;
use App\Support\AssetUrl;
use Illuminate\Http\Request;

class CustomerProductController extends Controller
{
    /**
     * Halaman home dengan slideshow + produk (dengan filter)
     */
    public function index(Request $request)
    {
        // Load slideshow slides
        $slides = KontenSlideShow::orderBy('urutan')->get();

        // Query produk dengan filter
        $query = Produk::query()
            ->with('sizeStocks')
            ->where('is_active', true);

        // Filter Search (nama produk, material, kategori, tags, deskripsi)
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';
            $query->where(function ($q) use ($like) {
                $q->where('nama_produk', 'LIKE', $like)
                    ->orWhere('material', 'LIKE', $like)
                    ->orWhere('proses', 'LIKE', $like)
                    ->orWhere('kategori', 'LIKE', $like)
                    ->orWhere('gender', 'LIKE', $like)
                    ->orWhere('jenis_lengan', 'LIKE', $like)
                    ->orWhere('sku', 'LIKE', $like)
                    ->orWhere('tags', 'LIKE', $like)
                    ->orWhere('deskripsi', 'LIKE', $like);
            });
        }

        // Filter Gender
        $gender = $this->cleanFilterValue($request->input('gender'));
        if ($gender) {
            $this->whereNormalizedEquals($query, ['gender', 'kategori'], $gender);
        }

        // Filter Jenis Lengan
        $sleeve = $this->cleanFilterValue($request->input('jenis_lengan'));
        if ($sleeve) {
            $this->whereNormalizedEquals($query, ['jenis_lengan'], $sleeve);
        }

        // Filter Harga Range
        $priceRange = $this->parsePriceRange($request->input('price_range'));
        if ($priceRange) {
            [$min, $max] = $priceRange;
            $query->whereRaw(
                '(CASE WHEN harga_diskon IS NOT NULL AND harga_diskon > 0 THEN harga_diskon ELSE harga END) BETWEEN ? AND ?',
                [$min, $max]
            );
        }

        // Load products dengan rating dari reviews
        $productService = app(ProductService::class);
        $products = $query->orderBy('id', 'desc')->get()->map(function ($p) use ($productService) {
            return $productService->formatProductWithRating($p);
        });
        $filterOptions = $this->filterOptions();

        return view('customer.pages.homePage', compact('products', 'slides', 'filterOptions'));
    }

    /**
     * Detail produk dengan reviews dan related products
     */
    public function show($id)
    {
        $product = Produk::with(['reviews.user', 'sizeStocks'])
            ->where('is_active', true)
            ->findOrFail($id);

        // Normalize product data
        $productData = [
            'id'        => $product->id,
            'nama'      => $product->nama_produk,
            'harga'     => $product->harga,
            'harga_diskon' => $product->harga_diskon ?? $product->harga,
            'diskon_persen' => $product->diskon_persen ?? 0,
            'gambar'    => $product->gambar,
            'deskripsi' => $product->deskripsi,
            'material'  => $product->material,
            'proses'    => $product->proses,
            'sku'       => $product->sku,
            'ukuran'    => $product->ukuran,
            'kategori'  => $product->kategori,
            'stok'      => $product->stok,
            'size_stocks' => $product->sizeStocks->mapWithKeys(fn ($stock) => [$stock->ukuran => (int) $stock->stok])->all(),
            'gambar_url'=> $product->image_url,
            'filosofi_motif' => $product->filosofi_motif,
        ]; 

        // Get visible reviews untuk produk ini
        $reviews = $product->reviews()
            ->with('user')
            ->where('moderation_status', 'approved')
            ->latest()
            ->get();

        // Get related products (produk lain, exclude current)
        $productService = app(ProductService::class);
        $relatedProducts = Produk::with('sizeStocks')
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get()
            ->map(function($p) use ($productService) {
                return $productService->formatProductWithRating($p);
            });

        return view('customer.pages.detailProduct', [
            'product' => $productData,
            'reviews' => $reviews,
            'relatedProducts' => $relatedProducts
        ]);
    }

    /**
     * Live search untuk autocomplete navbar
     */
    public function liveSearch()
    {
        $q = request('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Produk::where('is_active', true)
            ->where('nama_produk', 'LIKE', "%{$q}%")
            ->limit(6)
            ->get(['id', 'nama_produk', 'harga', 'harga_diskon', 'diskon_persen', 'gambar']);

        $formatted = $results->map(function($p) {
            return [
                'id'    => $p->id,
                'nama'  => $p->nama_produk,
                'harga' => $p->harga_diskon ?? $p->harga,
                'gambar'=> AssetUrl::product($p->gambar),
                'url'   => route('customer.product.detail', $p->id),
            ];
        });

        return response()->json($formatted);
    }

    private function cleanFilterValue(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $lower = strtolower($value);

        if (in_array($lower, ['semua', 'all'], true)) {
            return null;
        }

        return $value;
    }

    private function whereNormalizedEquals($query, array $columns, string $value): void
    {
        $normalized = strtolower($value);

        $query->where(function ($q) use ($columns, $normalized) {
            foreach ($columns as $column) {
                $q->orWhereRaw('LOWER(' . $column . ') = ?', [$normalized]);
            }
        });
    }

    private function parsePriceRange(mixed $value): ?array
    {
        $value = trim((string) $value);

        if ($value === '' || ! str_contains($value, '-')) {
            return null;
        }

        [$min, $max] = array_pad(explode('-', $value, 2), 2, null);
        $min = max(0, (int) preg_replace('/\D+/', '', (string) $min));
        $max = max(0, (int) preg_replace('/\D+/', '', (string) $max));

        if ($max <= 0 || $max < $min) {
            return null;
        }

        return [$min, $max];
    }

    private function filterOptions(): array
    {
        return [
            'genders' => $this->collectFilterOptions(['gender', 'kategori'], ['Pria', 'Wanita', 'Anak-anak']),
            'sleeves' => $this->collectFilterOptions(['jenis_lengan'], ['Pendek', 'Panjang']),
            'price_ranges' => [
                ['value' => '0-200000', 'label' => 'Di bawah Rp 200.000'],
                ['value' => '200000-400000', 'label' => 'Rp 200.000 - Rp 400.000'],
                ['value' => '400000-600000', 'label' => 'Rp 400.000 - Rp 600.000'],
                ['value' => '600000-800000', 'label' => 'Rp 600.000 - Rp 800.000'],
                ['value' => '800000-999999999', 'label' => 'Di atas Rp 800.000'],
            ],
        ];
    }

    private function collectFilterOptions(array $columns, array $preferred = []): array
    {
        $values = collect();

        foreach ($columns as $column) {
            $values = $values->merge(
                Produk::where('is_active', true)
                    ->whereNotNull($column)
                    ->pluck($column)
            );
        }

        $values = $values
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique(fn ($value) => strtolower($value))
            ->values();

        $ordered = collect($preferred)
            ->filter(fn ($preferredValue) => $values->contains(fn ($value) => strcasecmp($value, $preferredValue) === 0))
            ->values();

        $others = $values
            ->reject(fn ($value) => $ordered->contains(fn ($orderedValue) => strcasecmp($orderedValue, $value) === 0))
            ->sort()
            ->values();

        return $ordered->merge($others)->values()->all();
    }
}
