<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_products(): void
    {
        $currency = Currency::factory()->create(['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1.0]);
        Product::factory()->count(3)->create(['currency_id' => $currency->id]);

        $response = $this->getJson('/products');

        $response->assertStatus(200)
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'currency_id',
                        'tax_cost',
                        'manufacturing_cost',
                        'currency',
                        'prices',
                    ],
                ],
            ]);
    }

    public function test_can_filter_and_paginate_products_list(): void
    {
        $usd = Currency::factory()->create(['name' => 'USD']);
        $eur = Currency::factory()->create(['name' => 'EUR']);

        Product::factory()->create([
            'name' => 'iPhone 16 Pro',
            'description' => 'Smartphone Apple Titanio',
            'price' => 1199.00,
            'currency_id' => $usd->id,
        ]);

        Product::factory()->create([
            'name' => 'Galaxy S25 Ultra',
            'description' => 'Smartphone Samsung Android',
            'price' => 1299.00,
            'currency_id' => $usd->id,
        ]);

        Product::factory()->create([
            'name' => 'Cable USB-C',
            'description' => 'Accesorio de carga rápida',
            'price' => 19.99,
            'currency_id' => $eur->id,
        ]);

        // Filter by search
        $this->getJson('/products?search=Titanio')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'iPhone 16 Pro');

        // Filter by currency_id
        $this->getJson("/products?currency_id={$eur->id}")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Cable USB-C');

        // Filter by min_price & max_price
        $this->getJson('/products?min_price=1000&max_price=1250')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'iPhone 16 Pro');

        // Paginate results
        $this->getJson('/products?per_page=2')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_can_create_a_product(): void
    {
        $currency = Currency::factory()->create(['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1.0]);

        $payload = [
            'name' => 'Laptop Pro X',
            'description' => 'Laptop de última generación para desarrollo.',
            'price' => 1500.50,
            'currency_id' => $currency->id,
            'tax_cost' => 150.05,
            'manufacturing_cost' => 800.00,
        ];

        $response = $this->postJson('/products', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Laptop Pro X')
            ->assertJsonPath('data.price', 1500.5)
            ->assertJsonPath('data.currency_id', $currency->id);

        $this->assertDatabaseHas('products', [
            'name' => 'Laptop Pro X',
            'currency_id' => $currency->id,
        ]);
    }

    public function test_accepts_capitalized_price_field_and_sanitizes_xss_tags(): void
    {
        $currency = Currency::factory()->create(['name' => 'USD']);

        $response = $this->postJson('/products', [
            'name' => '  <script>alert(1)</script>Teclado Mecánico  ',
            'description' => '<b>Descripción segura</b> con tags HTML',
            'Price' => 250.00,
            'currency_id' => $currency->id,
            'tax_cost' => 25.00,
            'manufacturing_cost' => 100.00,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'alert(1)Teclado Mecánico')
            ->assertJsonPath('data.description', 'Descripción segura con tags HTML')
            ->assertJsonPath('data.price', 250);
    }

    public function test_validates_required_fields_when_creating_product(): void
    {
        $response = $this->postJson('/products', []);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors([
                'name',
                'description',
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
            ]);
    }

    public function test_rejects_negative_costs_and_non_existent_currency(): void
    {
        $response = $this->postJson('/products', [
            'name' => 'Producto Inválido',
            'description' => 'Prueba de validación',
            'price' => -10,
            'currency_id' => 99999,
            'tax_cost' => -5,
            'manufacturing_cost' => -1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
            ]);
    }

    public function test_can_get_a_single_product_by_id(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Monitor 4K',
            'currency_id' => $currency->id,
        ]);

        $response = $this->getJson("/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', 'Monitor 4K');
    }

    public function test_returns_404_when_product_not_found(): void
    {
        $response = $this->getJson('/products/99999');

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_can_update_a_product(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $response = $this->putJson("/products/{$product->id}", [
            'name' => 'Producto Actualizado',
            'price' => 999.99,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Producto Actualizado')
            ->assertJsonPath('data.price', 999.99);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Producto Actualizado',
            'price' => 999.99,
        ]);
    }

    public function test_returns_404_when_updating_non_existent_product(): void
    {
        $this->putJson('/products/99999', ['name' => 'No existe'])
            ->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_can_delete_a_product_and_subsequent_get_returns_404(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $response = $this->deleteJson("/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);

        $this->getJson("/products/{$product->id}")
            ->assertStatus(404);
    }
}
