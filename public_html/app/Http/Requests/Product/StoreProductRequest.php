<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize input fields (e.g. "Price" -> "price" from PDF spec) and sanitize strings.
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'tax_cost' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'manufacturing_cost' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'description.required' => 'La descripción del producto es obligatoria.',
            'price.required' => 'El precio del producto es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico válido.',
            'price.min' => 'El precio no puede ser negativo.',
            'currency_id.required' => 'El identificador de la divisa base (currency_id) es obligatorio.',
            'currency_id.exists' => 'La divisa seleccionada no existe en el sistema.',
            'tax_cost.required' => 'El costo de impuestos (tax_cost) es obligatorio.',
            'tax_cost.numeric' => 'El costo de impuestos debe ser numérico.',
            'manufacturing_cost.required' => 'El costo de fabricación (manufacturing_cost) es obligatorio.',
            'manufacturing_cost.numeric' => 'El costo de fabricación debe ser numérico.',
        ];
    }
}
