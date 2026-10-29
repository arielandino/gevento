<?php

namespace Tests\Unit\Services;

use App\Models\Currency;
use App\Models\Product;
use App\Services\ProductPriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductPriceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductPriceService;
    }

    public function test_store_for_product_creates_new_price_and_loads_currency(): void
    {
        $usd = Currency::factory()->create(['name' => 'USD']);
        $eur = Currency::factory()->create(['name' => 'EUR']);
        $product = Product::factory()->create(['currency_id' => $usd->id, 'price' => 100.00]);

        $productPrice = $this->service->storeForProduct($product, [
            'currency_id' => $eur->id,
            'price' => 92.50,
        ]);

        $this->assertEquals($product->id, $productPrice->product_id);
        $this->assertEquals($eur->id, $productPrice->currency_id);
        $this->assertEquals('92.50', $productPrice->price);
        $this->assertTrue($productPrice->relationLoaded('currency'));
        $this->assertEquals('EUR', $productPrice->currency->name);
    }

    public function test_store_for_product_is_idempotent_and_updates_existing_currency_price(): void
    {
        $usd = Currency::factory()->create(['name' => 'USD']);
        $eur = Currency::factory()->create(['name' => 'EUR']);
        $product = Product::factory()->create(['currency_id' => $usd->id, 'price' => 100.00]);

        $first = $this->service->storeForProduct($product, [
            'currency_id' => $eur->id,
            'price' => 90.00,
        ]);

        $second = $this->service->storeForProduct($product, [
            'currency_id' => $eur->id,
            'price' => 95.25,
        ]);

        $this->assertEquals($first->id, $second->id);
        $this->assertEquals('95.25', $second->price);
        $this->assertCount(1, $this->service->listByProduct($product));
    }
}
