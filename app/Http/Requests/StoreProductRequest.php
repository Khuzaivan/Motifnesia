<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya admin yang bisa tambah produk
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'description'   => 'required|string',
            'price'         => 'required|numeric|min:0',
            'category'      => 'required|string|max:100',
            'stock'         => 'required|integer|min:0',
            'material'      => 'required|string|max:100',
            'process'       => 'required|string|max:100',
            'sku'           => 'required|string|max:50|unique:produk,sku',
            'tags'          => 'required|string|max:255',
            'ukuran'        => 'required|string|max:10',
            'size_stocks'   => 'nullable|array',
            'size_stocks.*' => 'nullable|integer|min:0',
            'jenis_lengan'  => 'required|string|max:50',
            'diskon_persen' => 'nullable|integer|min:0|max:100',
            'image'         => 'required|image|mimes:png,jpg,jpeg,webp|max:5120',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name'          => 'nama produk',
            'description'   => 'deskripsi',
            'price'         => 'harga',
            'category'      => 'kategori',
            'stock'         => 'stok',
            'material'      => 'material',
            'process'       => 'proses',
            'sku'           => 'SKU',
            'tags'          => 'tags',
            'ukuran'        => 'ukuran',
            'size_stocks.*' => 'stok per ukuran',
            'jenis_lengan'  => 'jenis lengan',
            'diskon_persen' => 'diskon',
            'image'         => 'gambar',
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'     => ':attribute wajib diisi.',
            'price.min'         => ':attribute tidak boleh kurang dari 0.',
            'sku.unique'        => ':attribute sudah digunakan.',
            'image.required'    => ':attribute produk wajib diupload.',
            'image.image'       => ':attribute harus berupa gambar.',
            'image.max'         => 'Ukuran :attribute maksimal 5MB.',
        ];
    }
}
