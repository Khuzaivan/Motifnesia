<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('id'); // Get product ID from route
        
        return [
            'name'          => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'nullable|numeric|min:0',
            'category'      => 'nullable|string|max:100',
            'stock'         => 'nullable|integer|min:0',
            'material'      => 'nullable|string|max:100',
            'process'       => 'nullable|string|max:100',
            'sku'           => 'nullable|string|max:50|unique:produk,sku,' . $productId,
            'tags'          => 'nullable|string|max:255',
            'ukuran'        => 'nullable|string|max:10',
            'size_stocks'   => 'nullable|array',
            'size_stocks.*' => 'nullable|integer|min:0',
            'jenis_lengan'  => 'nullable|string|max:50',
            'diskon_persen' => 'nullable|integer|min:0|max:100',
            'image'         => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'notify_members' => 'nullable|boolean',
            'filosofi_motif' => 'nullable|string',
        ];
    }

    /**
     * Get custom attributes for validator errors.
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
     */
    public function messages(): array
    {
        return [
            'price.min'     => ':attribute tidak boleh kurang dari 0.',
            'sku.unique'    => ':attribute sudah digunakan.',
            'image.image'   => ':attribute harus berupa gambar.',
            'image.max'     => 'Ukuran :attribute maksimal 5MB.',
        ];
    }
}
