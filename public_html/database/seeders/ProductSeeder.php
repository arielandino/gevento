<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usd = Currency::where('name', 'USD')->first();
        $eur = Currency::where('name', 'EUR')->first();
        $ars = Currency::where('name', 'ARS')->first();
        $mxn = Currency::where('name', 'MXN')->first();

        if (! $usd) {
            return;
        }

        $products = [
            [
                'name' => 'MacBook Pro 16" M3 Max',
                'description' => 'Portátil profesional de alto rendimiento con chip M3 Max, 36GB RAM y 1TB SSD.',
                'price' => 3499.00,
                'currency_id' => $usd->id,
                'tax_cost' => 349.90,
                'manufacturing_cost' => 1850.00,
            ],
            [
                'name' => 'Monitor Dell UltraSharp 27" 4K USB-C',
                'description' => 'Monitor IPS 4K UHD con Hub USB-C integrado y calibración de color de fábrica.',
                'price' => 649.99,
                'currency_id' => $usd->id,
                'tax_cost' => 65.00,
                'manufacturing_cost' => 310.00,
            ],
            [
                'name' => 'Teclado Mecánico Keychron Q1 Pro',
                'description' => 'Teclado mecánico inalámbrico custom de aluminio CNC con soporte QMK/VIA.',
                'price' => 199.00,
                'currency_id' => $usd->id,
                'tax_cost' => 19.90,
                'manufacturing_cost' => 85.00,
            ],
        ];

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['name' => $data['name']],
                $data
            );

            foreach ([$eur, $ars, $mxn] as $targetCurrency) {
                if ($targetCurrency) {
                    ProductPrice::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'currency_id' => $targetCurrency->id,
                        ],
                        [
                            'price' => round((float) $product->price * (float) $targetCurrency->exchange_rate, 2),
                        ]
                    );
                }
            }
        }
    }
}
