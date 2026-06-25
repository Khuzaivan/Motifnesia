<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiProductController extends Controller
{
    /**
     * ========================================
     * GET ALL PRODUCTS (dengan pagination & filter)
     * ========================================
     * Endpoint: GET /api/products
     * Query params: 
     *   - per_page (default: 10)
     *   - category (filter by kategori)
     *   - search (search by nama)
     */
    public function index(Request $request)
    {
        try {
            // Ambil query params
            $perPage = $request->get('per_page', 10);
            $category = $request->get('category');
            $search = $request->get('search');

            // Query builder
            $query = Produk::query()->where('is_active', true);

            // Filter by category (jika ada)
            if ($category) {
                $query->where('kategori', $category);
            }

            // Search by nama produk (jika ada)
            if ($search) {
                $query->where('nama_produk', 'like', "%{$search}%");
            }

            // Order by terbaru & paginate
            $products = $query->orderBy('id', 'desc')->paginate($perPage);

            // Return success response dengan pagination
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diambil',
                'data' => $products->items(), // Array produk
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            // Return error response jika ada exception
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * GET SINGLE PRODUCT BY ID
     * ========================================
     * Endpoint: GET /api/products/{id}
     */
    public function show($id)
    {
        try {
            // Cari produk by ID
            $product = Produk::where('is_active', true)->find($id);

            // Cek apakah produk ditemukan
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Detail produk berhasil diambil',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * CREATE NEW PRODUCT
     * ========================================
     * Endpoint: POST /api/products
     * Body (form-data or JSON):
     *   - name (required)
     *   - description (required)
     *   - price (required, numeric)
     *   - stock (required, numeric)
     *   - category (required)
     *   - material (optional)
     *   - process (optional)
     *   - sku (optional)
     *   - tags (optional)
     *   - ukuran (optional)
     *   - jenis_lengan (optional)
     *   - diskon_persen (optional, 0-100)
     *   - image (optional, file gambar)
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category' => 'required|string',
                'material' => 'nullable|string',
                'process' => 'nullable|string',
                'sku' => 'nullable|string|unique:produk,sku',
                'tags' => 'nullable|string',
                'ukuran' => 'nullable|string',
                'jenis_lengan' => 'nullable|string',
                'diskon_persen' => 'nullable|integer|min:0|max:100',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            ]);

            // Jika validasi gagal, return error
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422); // 422 = Unprocessable Entity
            }

            // Handle upload gambar (jika ada)
            $gambarPath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/products'), $imageName);
                $gambarPath = 'images/products/' . $imageName;
            }

            // Kalkulasi harga diskon
            $diskonPersen = $request->input('diskon_persen', 0);
            $harga = $request->input('price');
            $hargaDiskon = $harga - ($harga * $diskonPersen / 100);

            // Buat produk baru
            $product = Produk::create([
                'nama_produk' => $request->name,
                'deskripsi' => $request->description,
                'harga' => $harga,
                'stok' => $request->stock,
                'kategori' => $request->category,
                'material' => $request->material,
                'proses' => $request->process,
                'sku' => $request->sku,
                'tags' => $request->tags,
                'ukuran' => $request->ukuran,
                'jenis_lengan' => $request->jenis_lengan,
                'diskon_persen' => $diskonPersen,
                'harga_diskon' => $hargaDiskon,
                'gambar' => $gambarPath,
                'is_active' => true,
            ]);

            // Return success response dengan data produk yang baru dibuat
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ], 201); // 201 = Created

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * UPDATE PRODUCT
     * ========================================
     * Endpoint: PUT/PATCH /api/products/{id}
     * Body: sama seperti create (tapi semua optional)
     */
    public function update(Request $request, $id)
    {
        try {
            // Cari produk by ID
            $product = Produk::find($id);

            // Cek apakah produk ditemukan
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            // Validasi input (semua optional untuk update)
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'stock' => 'nullable|integer|min:0',
                'category' => 'nullable|string',
                'material' => 'nullable|string',
                'process' => 'nullable|string',
                'sku' => 'nullable|string|unique:produk,sku,' . $id,
                'tags' => 'nullable|string',
                'ukuran' => 'nullable|string',
                'jenis_lengan' => 'nullable|string',
                'diskon_persen' => 'nullable|integer|min:0|max:100',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle upload gambar baru (jika ada)
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($product->gambar && file_exists(public_path($product->gambar))) {
                    unlink(public_path($product->gambar));
                }

                // Upload gambar baru
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/products'), $imageName);
                $product->gambar = 'images/products/' . $imageName;
            }

            // Update field yang ada di request
            if ($request->has('name')) {
                $product->nama_produk = $request->name;
            }
            if ($request->has('description')) {
                $product->deskripsi = $request->description;
            }
            if ($request->has('price')) {
                $product->harga = $request->price;
            }
            if ($request->has('stock')) {
                $product->stok = $request->stock;
            }
            if ($request->has('category')) {
                $product->kategori = $request->category;
            }
            if ($request->has('material')) {
                $product->material = $request->material;
            }
            if ($request->has('process')) {
                $product->proses = $request->process;
            }
            if ($request->has('sku')) {
                $product->sku = $request->sku;
            }
            if ($request->has('tags')) {
                $product->tags = $request->tags;
            }
            if ($request->has('ukuran')) {
                $product->ukuran = $request->ukuran;
            }
            if ($request->has('jenis_lengan')) {
                $product->jenis_lengan = $request->jenis_lengan;
            }
            if ($request->has('diskon_persen')) {
                $product->diskon_persen = $request->diskon_persen;
            }

            // Kalkulasi ulang harga diskon
            $product->harga_diskon = $product->harga - ($product->harga * $product->diskon_persen / 100);

            // Save perubahan
            $product->save();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * DELETE PRODUCT
     * ========================================
     * Endpoint: DELETE /api/products/{id}
     */
    public function destroy($id)
    {
        try {
            // Cari produk by ID
            $product = Produk::find($id);

            // Cek apakah produk ditemukan
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            $product->update([
                'is_active' => false,
                'archived_at' => now(),
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diarsipkan'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * GET CATEGORIES (untuk dropdown di Flutter)
     * ========================================
     * Endpoint: GET /api/products/categories
     */
    public function categories()
    {
        try {
            // Ambil semua kategori unik dari produk
            $categories = Produk::where('is_active', true)->distinct()->pluck('kategori')->filter();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diambil',
                'data' => $categories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * SEARCH PRODUCTS (pencarian advanced)
     * ========================================
     * Endpoint: POST /api/products/search
     * Body:
     *   - keyword (required)
     *   - min_price (optional)
     *   - max_price (optional)
     *   - category (optional)
     */
    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'keyword' => 'required|string|min:1',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'category' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Produk::query()->where('is_active', true);

            // Search by keyword (nama atau deskripsi)
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('nama_produk', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi', 'like', "%{$keyword}%")
                  ->orWhere('tags', 'like', "%{$keyword}%");
            });

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('harga', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('harga', '<=', $request->max_price);
            }

            // Filter by category
            if ($request->has('category')) {
                $query->where('kategori', $request->category);
            }

            $products = $query->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Pencarian berhasil',
                'data' => $products,
                'total_results' => $products->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
