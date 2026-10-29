<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => strtoupper($this->faker->unique()->lexify('???')).' - '.$this->faker->currencyCode(),
            'symbol' => $this->faker->randomElement(['$', '€', '£', '¥', 'R$']),
            'exchange_rate' => $this->faker->randomFloat(4, 0.5, 1200),
        ];
    }
}
