<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('transaction')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:income,expense'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'amount' => ['required', 'integer', 'min:1000', 'max:1000000000000'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:2000'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'extensions:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['type', 'category_id'])) {
                    return;
                }

                $categoryMatchesType = Category::query()
                    ->whereKey($this->integer('category_id'))
                    ->where('type', $this->string('type')->toString())
                    ->exists();

                if (! $categoryMatchesType) {
                    $validator->errors()->add('category_id', 'Kategori tidak sesuai dengan jenis transaksi.');
                }
            },
        ];
    }
}
