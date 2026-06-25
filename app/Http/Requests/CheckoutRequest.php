<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CheckoutRequest extends FormRequest
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
            'alamat'                 => 'required|string|min:10',
            'metode_pengiriman_id'   => 'required|exists:metode_pengiriman,id',
            'metode_pembayaran_id'   => 'required|exists:metode_pembayaran,id',
            'voucher_code'           => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'alamat'                 => 'alamat pengiriman',
            'metode_pengiriman_id'   => 'metode pengiriman',
            'metode_pembayaran_id'   => 'metode pembayaran',
            'voucher_code'           => 'voucher membership',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'alamat.required'                 => 'Alamat pengiriman wajib diisi.',
            'alamat.min'                      => 'Alamat minimal 10 karakter.',
            'metode_pengiriman_id.required'   => 'Pilih metode pengiriman.',
            'metode_pengiriman_id.exists'     => 'Metode pengiriman tidak valid.',
            'metode_pembayaran_id.required'   => 'Pilih metode pembayaran.',
            'metode_pembayaran_id.exists'     => 'Metode pembayaran tidak valid.',
        ];
    }
}
