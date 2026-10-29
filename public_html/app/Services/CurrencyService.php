<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CurrencyService
{
    /**
     * List all available currencies.
     *
     * @return Collection<int, Currency>
     */
    public function list(): Collection
    {
        return Currency::orderBy('id', 'asc')->get();
    }

    /**
     * Find a currency by ID or fail.
     */
    public function findOrFail(int $id): Currency
    {
        return Currency::findOrFail($id);
    }

    /**
     * Create a new currency.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Currency
    {
        return DB::transaction(function () use ($data) {
            return Currency::create($data);
        });
    }

    /**
     * Update an existing currency.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Currency $currency, array $data): Currency
    {
        return DB::transaction(function () use ($currency, $data) {
            $currency->update($data);

            return $currency->refresh();
        });
    }

    /**
     * Delete a currency.
     */
    public function delete(Currency $currency): void
    {
        DB::transaction(function () use ($currency) {
            $currency->delete();
        });
    }
}
