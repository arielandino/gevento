<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $manufacturingCost = $this->faker->randomFloat(2, 10, 500);
        $taxCost = round($manufacturingCost * 0.21, 2);
        $price = round(($manufacturingCost + $taxCost) * 1.35, 2);

        return [
            'name' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->sentence(12),
            'price' => $price,
            'currency_id' => Currency::factory(),
            'tax_cost' => $taxCost,
            'manufacturing_cost' => $manufacturingCost,
        ];
    }
}
