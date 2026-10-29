<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_currencies(): void
    {
        Currency::factory()->create(['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1.0]);
        Currency::factory()->create(['name' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.92]);

        $this->getJson('/currencies')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_a_currency_and_uppercases_name(): void
    {
        $response = $this->postJson('/currencies', [
            'name' => 'brl',
            'symbol' => 'R$',
            'exchange_rate' => 5.65,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'BRL')
            ->assertJsonPath('data.symbol', 'R$')
            ->assertJsonPath('data.exchange_rate', 5.65);

        $this->assertDatabaseHas('currencies', ['name' => 'BRL']);
    }

    public function test_rejects_duplicate_currency_name_or_invalid_exchange_rate(): void
    {
        Currency::factory()->create(['name' => 'USD']);

        $this->postJson('/currencies', [
            'name' => 'usd',
            'symbol' => '$',
            'exchange_rate' => 0,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'exchange_rate']);
    }

    public function test_can_show_and_update_a_currency(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'ARS',
            'symbol' => '$',
            'exchange_rate' => 1000.00,
        ]);

        $this->getJson("/currencies/{$currency->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'ARS');

        $this->putJson("/currencies/{$currency->id}", [
            'name' => 'ARS',
            'exchange_rate' => 1150.50,
        ])->assertStatus(200)
            ->assertJsonPath('data.exchange_rate', 1150.5);
    }

    public function test_can_delete_unused_currency_but_blocks_deleting_currency_in_use(): void
    {
        $unused = Currency::factory()->create(['name' => 'CLP']);
        $used = Currency::factory()->create(['name' => 'USD']);
        Product::factory()->create(['currency_id' => $used->id]);

        // Deleting currency in use returns 409 Conflict
        $this->deleteJson("/currencies/{$used->id}")
            ->assertStatus(409)
            ->assertJsonPath('success', false);

        // Deleting unused currency returns 200 OK
        $this->deleteJson("/currencies/{$unused->id}")
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('currencies', ['id' => $unused->id]);
    }
}
