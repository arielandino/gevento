<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductPriceService
{
    /**
     * Get all registered prices for a specific product.
     *
     * @return Collection<int, ProductPrice>
     */
    public function listByProduct(Product $product): Collection
    {
        return $product->prices()
            ->with('currency')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Register or update a price for a product in a specific currency.
     *
     * @param  array<string, mixed>  $data
     */
    public function storeForProduct(Product $product, array $data): ProductPrice
    {
        return DB::transaction(function () use ($product, $data) {
            $productPrice = ProductPrice::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'currency_id' => (int) $data['currency_id'],
                ],
                [
                    'price' => $data['price'],
                ]
            );

            return $productPrice->load('currency');
        });
    }
}
