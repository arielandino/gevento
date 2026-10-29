<?php

namespace App\Http\Requests\ProductPrice;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('price') && $this->has('Price')) {
            $this->merge(['price' => $this->input('Price')]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'currency_id.required' => 'El identificador de la divisa (currency_id) es obligatorio.',
            'currency_id.exists' => 'La divisa especificada no existe en el sistema.',
            'price.required' => 'El precio del producto en la divisa especificada es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico válido.',
            'price.min' => 'El precio no puede ser negativo.',
        ];
    }
}
