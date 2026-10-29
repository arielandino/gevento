<?php

namespace Tests\Unit\Services;

use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    private CurrencyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CurrencyService;
    }

    public function test_can_create_find_update_and_delete_currency(): void
    {
        $created = $this->service->create([
            'name' => 'GBP',
            'symbol' => '£',
            'exchange_rate' => 0.7900,
        ]);

        $this->assertNotNull($created->id);
        $this->assertEquals('GBP', $created->name);

        $found = $this->service->findOrFail($created->id);
        $this->assertEquals($created->id, $found->id);

        $updated = $this->service->update($found, [
            'exchange_rate' => 0.8100,
        ]);
        $this->assertEquals('0.8100', $updated->exchange_rate);

        $this->service->delete($updated);
        $this->assertDatabaseMissing('currencies', ['id' => $created->id]);
    }

    public function test_find_or_fail_throws_exception_when_currency_does_not_exist(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findOrFail(99999);
    }
}
