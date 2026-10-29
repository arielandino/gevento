<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_prices_for_a_product(): void
    {
        $usd = Currency::factory()->create(['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1.0]);
        $eur = Currency::factory()->create(['name' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.92]);
        $ars = Currency::factory()->create(['name' => 'ARS', 'symbol' => '$', 'exchange_rate' => 1150.0]);

        $product = Product::factory()->create([
            'currency_id' => $usd->id,
            'price' => 100.00,
        ]);

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => 92.00,
        ]);

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $ars->id,
            'price' => 115000.00,
        ]);

        $response = $this->getJson("/products/{$product->id}/prices");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'product_id',
                        'currency_id',
                        'price',
                        'currency',
                    ],
                ],
            ]);
    }

    public function test_returns_404_when_listing_prices_for_non_existent_product(): void
    {
        $this->getJson('/products/99999/prices')
            ->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_can_create_a_new_price_for_a_product(): void
    {
        $usd = Currency::factory()->create(['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1.0]);
        $eur = Currency::factory()->create(['name' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.92]);

        $product = Product::factory()->create([
            'currency_id' => $usd->id,
            'price' => 200.00,
        ]);

        $response = $this->postJson("/products/{$product->id}/prices", [
            'currency_id' => $eur->id,
            'price' => 184.00,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.product_id', $product->id)
            ->assertJsonPath('data.currency_id', $eur->id)
            ->assertJsonPath('data.price', 184);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => 184.00,
        ]);
    }

    public function test_posting_existing_currency_for_same_product_updates_price_without_duplicating(): void
    {
        $usd = Currency::factory()->create(['name' => 'USD']);
        $eur = Currency::factory()->create(['name' => 'EUR']);
        $product = Product::factory()->create(['currency_id' => $usd->id]);

        $this->postJson("/products/{$product->id}/prices", [
            'currency_id' => $eur->id,
            'price' => 180.00,
        ])->assertStatus(201);

        $this->postJson("/products/{$product->id}/prices", [
            'currency_id' => $eur->id,
            'price' => 195.50,
        ])->assertStatus(201)
            ->assertJsonPath('data.price', 195.5);

        $this->assertDatabaseCount('product_prices', 1);
    }

    public function test_validates_currency_and_price_when_creating_product_price(): void
    {
        $usd = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $usd->id]);

        $this->postJson("/products/{$product->id}/prices", [
            'currency_id' => 99999,
            'price' => -20,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['currency_id', 'price']);
    }

    public function test_returns_404_when_creating_price_for_non_existent_product(): void
    {
        $eur = Currency::factory()->create();

        $this->postJson('/products/99999/prices', [
            'currency_id' => $eur->id,
            'price' => 100.00,
        ])->assertStatus(404);
    }
}
