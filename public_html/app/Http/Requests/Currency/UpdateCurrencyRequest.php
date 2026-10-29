<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name') && is_string($this->input('name'))) {
            $this->merge(['name' => strtoupper(trim(strip_tags($this->input('name'))))]);
        }

        if ($this->has('symbol') && is_string($this->input('symbol'))) {
            $this->merge(['symbol' => trim(strip_tags($this->input('symbol')))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $currencyId = $this->route('id') ?? $this->route('currency');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('currencies', 'name')->ignore($currencyId)],
            'symbol' => ['sometimes', 'required', 'string', 'max:10'],
            'exchange_rate' => ['sometimes', 'required', 'numeric', 'gt:0', 'max:99999999999.9999'],
        ];
    }
}
