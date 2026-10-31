<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'donor_name' => [Rule::requiredIf(! $this->boolean('is_anonymous')), 'nullable', 'string', 'max:120'],
            'is_anonymous' => ['required', 'boolean'],
            'amount' => ['required', 'integer', 'min:1000', 'max:1000000000'],
            'purpose' => ['required', Rule::in(['infaq_umum', 'infaq_jumat', 'pembangunan', 'zakat', 'sosial'])],
            'message' => ['nullable', 'string', 'max:1000'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'extensions:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'donor_name.required' => 'Nama donatur wajib diisi jika tidak memilih anonim.',
            'amount.min' => 'Nominal infaq minimal Rp1.000.',
            'purpose.in' => 'Tujuan infaq tidak tersedia.',
            'proof_file.mimes' => 'Bukti transfer harus berupa foto JPG/PNG atau dokumen PDF.',
            'proof_file.extensions' => 'Ekstensi bukti transfer harus JPG, PNG, atau PDF.',
            'proof_file.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ];
    }
}
