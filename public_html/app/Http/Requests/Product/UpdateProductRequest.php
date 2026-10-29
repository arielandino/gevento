<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize input fields and sanitize strings.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('price') && $this->has('Price')) {
            $this->merge(['price' => $this->input('Price')]);
        }

        if ($this->has('name') && is_string($this->input('name'))) {
            $this->merge(['name' => trim(strip_tags($this->input('name')))]);
        }

        if ($this->has('description') && is_string($this->input('description'))) {
            $this->merge(['description' => trim(strip_tags($this->input('description')))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string', 'max:5000'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'currency_id' => ['sometimes', 'required', 'integer', 'exists:currencies,id'],
            'tax_cost' => ['sometimes', 'required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'manufacturing_cost' => ['sometimes', 'required', 'numeric', 'min:0', 'max:9999999999999.99'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'currency_id.exists' => 'La divisa seleccionada no existe en el sistema.',
            'price.min' => 'El precio no puede ser negativo.',
            'tax_cost.min' => 'El costo de impuestos no puede ser negativo.',
            'manufacturing_cost.min' => 'El costo de fabricación no puede ser negativo.',
        ];
    }
}
