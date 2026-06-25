<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Produk;
use App\Models\User;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminProductController extends Controller
{
    // =========================
    // DAFTAR PRODUK (legacy route)
    // =========================
    public function index()
    {
        Gate::authorize('is-kasir');
        return redirect()->route('admin.product.management.index');
    }

    // =========================
    // CREATE & STORE PRODUK (Form + Save)
    // =========================
    public function createOrStore(Request $request, ProductService $productService)
    {
        Gate::authorize('is-owner');
        // Jika POST request, proses penyimpanan produk
        if ($request->isMethod('post')) {
            try {
                // Validasi menggunakan StoreProductRequest rules
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'required|string',
                    'price' => 'required|numeric|min:0',
                    'category' => 'required|string',
                    'stock' => 'required|integer|min:0',
                    'size_stocks' => 'nullable|array',
                    'size_stocks.*' => 'nullable|integer|min:0',
                    'material' => 'required|string',
                    'process' => 'required|string',
                    'sku' => 'required|string|unique:produk,sku',
                    'tags' => 'required|string',
                    'ukuran' => 'required|string',
                    'jenis_lengan' => 'required|string',
                    'diskon_persen' => 'required|integer|min:0|max:100',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'notify_members' => 'nullable|boolean',
                    'filosofi_motif' => 'nullable|string',
                ]);
            
                $gambarPath = null;
                if ($request->hasFile('image')) {
                    $gambarPath = $productService->uploadProductImage($request->file('image'));
                }

                // Prepare product data dengan kalkulasi diskon
                $productData = $productService->prepareProductData($validated, $gambarPath);

                // Simpan ke DB
                $produk = Produk::create(attributes: $productData);
                $productService->syncSizeStocks($produk, $productService->normalizeSizeStocks($validated));

                if ($request->boolean('notify_members')) {
                    $notifiedCount = $this->notifyMembersAboutProduct($produk, 'new');

                    return redirect()->route('admin.product.management.index')
                        ->with('success', 'Produk berhasil ditambahkan dan info produk dikirim ke ' . $notifiedCount . ' member aktif.');
                }

                return redirect()->route('admin.product.management.index')
                    ->with('success', 'Produk berhasil ditambahkan!');
            } catch (\Exception $e) {
                \Log::error('Error creating product: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
            }
        }

        // Jika GET request, tampilkan form
        $product = [
            'name' => '',
            'price' => '',
            'material' => '',
            'process' => '',
            'sku' => '',
            'category' => '',
            'tags' => '',
            'ukuran' => '',
            'jenis_lengan' => '',
            'stock' => '',
            'description' => '',
            'filosofi_motif' => '',
            'image' => \App\Support\AssetUrl::product(null),
        ];

        return view('admin.pages.addProduct', [
            'product' => $product,
            'formTitle' => 'Tambah Produk',
            'activePage' => 'products-create'
        ]);
    }

    // =========================
    // LIST / TABLE UNTUK EDIT & DELETE (Product Management)
    // =========================
    public function manage()
    {
        Gate::authorize('is-kasir');
        $products = Produk::with('sizeStocks')
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.pages.productManagement', [
            'products' => $products,
            'activePage' => 'product-management'
        ]);
    }

    // =========================
    // UPDATE PRODUK (MODAL)
    // =========================
    public function update(UpdateProductRequest $request, $id, ProductService $productService)
    {
        Gate::authorize('is-owner');
        $produk = Produk::findOrFail($id);
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($produk->gambar) {
                $productService->deleteProductImage($produk->gambar);
            }
            
            // Upload gambar baru
            $produk->gambar = $productService->uploadProductImage($request->file('image'));
        }

        // Update fields yang diinput
        if (isset($data['name'])) $produk->nama_produk = $data['name'];
        if (isset($data['description'])) $produk->deskripsi = $data['description'];
        if (isset($data['price'])) $produk->harga = $data['price'];
        if (isset($data['category'])) {
            $produk->kategori = $data['category'];
            $produk->gender = $productService->normalizeGenderCategory($data['category']);
        }
        if (isset($data['stock'])) $produk->stok = $data['stock'];
        if (isset($data['material'])) $produk->material = $data['material'];
        if (isset($data['process'])) $produk->proses = $data['process'];
        if (isset($data['sku'])) $produk->sku = $data['sku'];
        if (isset($data['tags'])) $produk->tags = $data['tags'];
        if (isset($data['ukuran'])) $produk->ukuran = $data['ukuran'];
        if (isset($data['jenis_lengan'])) $produk->jenis_lengan = $data['jenis_lengan'];
        if (array_key_exists('filosofi_motif', $data)) $produk->filosofi_motif = $data['filosofi_motif'];
        
        // Update diskon dan kalkulasi harga_diskon
        if (isset($data['diskon_persen'])) {
            $produk->diskon_persen = $data['diskon_persen'];
        }
        
        // Kalkulasi ulang harga_diskon menggunakan service
        $produk->harga_diskon = $productService->calculateDiscountedPrice(
            (float) $produk->harga, 
            (int) ($produk->diskon_persen ?? 0)
        );

        $produk->save();
        $productService->syncSizeStocks($produk, $productService->normalizeSizeStocks($data + ['stock' => $produk->stok]));

        $message = 'Produk berhasil diupdate!';

        if ($request->boolean('notify_members')) {
            $notifiedCount = $this->notifyMembersAboutProduct($produk, 'promo');
            $message = 'Produk berhasil diupdate dan promo dikirim ke ' . $notifiedCount . ' member aktif.';
        }

        return response()->json([
            'success' => true, 
            'message' => $message
        ]);
    }

    // =========================
    // HAPUS PRODUK
    // =========================
    public function destroy($id, ProductService $productService)
    {
        Gate::authorize('is-owner');
        $produk = Produk::findOrFail($id);

        $produk->update([
            'is_active' => false,
            'archived_at' => now(),
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Produk berhasil diarsipkan!'
        ]);
    }

    private function notifyMembersAboutProduct(Produk $produk, string $context = 'new'): int
    {
        $members = User::where('is_member', true)
            ->where('membership_status', 'active')
            ->get(['id']);

        $isPromo = $context === 'promo';

        foreach ($members as $member) {
            Notification::create([
                'user_id' => $member->id,
                'type' => $isPromo ? 'member_special_promo' : 'member_new_product',
                'title' => $isPromo ? 'Promo Produk untuk Member' : 'Produk Baru untuk Member',
                'message' => $produk->nama_produk . ($isPromo ? ' sedang punya update promo member dengan harga Rp ' : ' baru saja tersedia dengan harga Rp ') . number_format((float) ($produk->harga_diskon ?: $produk->harga), 0, ',', '.') . '.',
                'link' => route('customer.product.detail', $produk->id),
                'priority' => 'info',
                'is_read' => false,
                'data' => [
                    'produk_id' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'harga' => (float) ($produk->harga_diskon ?: $produk->harga),
                ],
            ]);
        }

        return $members->count();
    }
}
