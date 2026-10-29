<?php

namespace Tests\Unit\Services;

use App\Models\Currency;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductService;
    }

    public function test_list_returns_collection_by_default(): void
    {
        $currency = Currency::factory()->create();
        Product::factory()->count(4)->create(['currency_id' => $currency->id]);

        $result = $this->service->list();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(4, $result);
    }

    public function test_list_returns_paginator_when_per_page_is_provided(): void
    {
        $currency = Currency::factory()->create();
        Product::factory()->count(5)->create(['currency_id' => $currency->id]);

        $result = $this->service->list(['per_page' => 2]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(2, $result->items());
        $this->assertEquals(5, $result->total());
    }

    public function test_list_filters_by_search_currency_and_price_range(): void
    {
        $usd = Currency::factory()->create(['name' => 'USD']);
        $eur = Currency::factory()->create(['name' => 'EUR']);

        Product::factory()->create([
            'name' => 'Teclado Gamer RGB',
            'description' => 'Switch mecánico rojo',
            'price' => 120.00,
            'currency_id' => $usd->id,
        ]);

        Product::factory()->create([
            'name' => 'Mouse Inalámbrico',
            'description' => 'Sensor óptico 25K',
            'price' => 80.00,
            'currency_id' => $usd->id,
        ]);

        Product::factory()->create([
            'name' => 'Monitor Curvo',
            'description' => 'Panel OLED 240Hz',
            'price' => 900.00,
            'currency_id' => $eur->id,
        ]);

        $bySearch = $this->service->list(['search' => 'Teclado']);
        $this->assertCount(1, $bySearch);
        $this->assertEquals('Teclado Gamer RGB', $bySearch->first()->name);

        $byCurrency = $this->service->list(['currency_id' => $eur->id]);
        $this->assertCount(1, $byCurrency);
        $this->assertEquals('Monitor Curvo', $byCurrency->first()->name);

        $byPriceRange = $this->service->list(['min_price' => 100, 'max_price' => 500]);
        $this->assertCount(1, $byPriceRange);
        $this->assertEquals('Teclado Gamer RGB', $byPriceRange->first()->name);
    }

    public function test_create_persists_product_and_loads_relations(): void
    {
        $currency = Currency::factory()->create(['name' => 'USD']);

        $product = $this->service->create([
            'name' => 'Servidor Rack 2U',
            'description' => 'Servidor empresarial dual Xeon',
            'price' => 4500.00,
            'currency_id' => $currency->id,
            'tax_cost' => 450.00,
            'manufacturing_cost' => 2800.00,
        ]);

        $this->assertNotNull($product->id);
        $this->assertTrue($product->relationLoaded('currency'));
        $this->assertTrue($product->relationLoaded('prices'));
        $this->assertEquals('USD', $product->currency->name);
    }

    public function test_find_or_fail_throws_exception_for_missing_product(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findOrFail(99999);
    }

    public function test_update_modifies_product_attributes(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Nombre Original',
            'price' => 100.00,
            'currency_id' => $currency->id,
        ]);

        $updated = $this->service->update($product, [
            'name' => 'Nombre Modificado',
            'price' => 150.75,
        ]);

        $this->assertEquals('Nombre Modificado', $updated->name);
        $this->assertEquals('150.75', $updated->price);
    }

    public function test_delete_soft_deletes_product(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $this->service->delete($product);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
