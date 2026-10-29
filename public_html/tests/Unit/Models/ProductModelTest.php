<?php

namespace Tests\Unit\Models;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_currency_and_has_many_prices(): void
    {
        $usd = Currency::factory()->create(['name' => 'USD']);
        $eur = Currency::factory()->create(['name' => 'EUR']);

        $product = Product::factory()->create([
            'currency_id' => $usd->id,
            'price' => 250.50,
            'tax_cost' => 25.05,
            'manufacturing_cost' => 110.00,
        ]);

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => 230.46,
        ]);

        $this->assertInstanceOf(Currency::class, $product->currency);
        $this->assertEquals('USD', $product->currency->name);
        $this->assertCount(1, $product->prices);
        $this->assertInstanceOf(ProductPrice::class, $product->prices->first());
        $this->assertEquals('250.50', $product->price);
        $this->assertEquals('25.05', $product->tax_cost);
        $this->assertEquals('110.00', $product->manufacturing_cost);
    }
}
