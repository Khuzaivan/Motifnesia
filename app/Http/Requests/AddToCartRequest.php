<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:produk,id',
            'ukuran'     => 'required|string|in:S,M,L,XL',
            'qty'        => 'nullable|integer|min:1|max:99',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'produk',
            'ukuran'     => 'ukuran',
            'qty'        => 'jumlah',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk tidak valid.',
            'product_id.exists'   => 'Produk tidak ditemukan.',
            'ukuran.required'     => 'Silakan pilih ukuran.',
            'ukuran.in'           => 'Ukuran tidak valid.',
            'qty.min'             => 'Jumlah minimal 1.',
            'qty.max'             => 'Jumlah maksimal 99.',
        ];
    }
}
