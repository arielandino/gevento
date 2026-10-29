<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'USD',
                'symbol' => '$',
                'exchange_rate' => 1.0000,
            ],
            [
                'name' => 'EUR',
                'symbol' => '€',
                'exchange_rate' => 0.9200,
            ],
            [
                'name' => 'ARS',
                'symbol' => '$',
                'exchange_rate' => 1150.0000,
            ],
            [
                'name' => 'MXN',
                'symbol' => '$',
                'exchange_rate' => 19.8500,
            ],
            [
                'name' => 'COP',
                'symbol' => '$',
                'exchange_rate' => 4250.0000,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['name' => $currency['name']],
                $currency
            );
        }
    }
}
