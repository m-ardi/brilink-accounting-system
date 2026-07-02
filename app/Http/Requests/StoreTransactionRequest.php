<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth sudah dijaga di route middleware
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'nominal'     => ['required', 'numeric', 'min:1000', 'max:50000000'],
            'keterangan'  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori layanan wajib dipilih.',
            'category_id.exists'   => 'Kategori layanan tidak valid.',
            'nominal.required'     => 'Nominal transaksi wajib diisi.',
            'nominal.numeric'      => 'Nominal harus berupa angka.',
            'nominal.min'          => 'Nominal minimal Rp 1.000.',
            'nominal.max'          => 'Nominal maksimal Rp 50.000.000.',
            'keterangan.max'       => 'Keterangan maksimal 500 karakter.',
        ];
    }
}
