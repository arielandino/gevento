<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Retrieve a filtered and sorted list of products (paginated or full collection).
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Product>|Collection<int, Product>
     */
    public function list(array $filters = []): LengthAwarePaginator|Collection
    {
        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDir = $filters['sort_dir'] ?? 'desc';

        $query = Product::query()
            ->with(['currency', 'prices.currency'])
            ->filter($filters)
            ->orderBy($sortBy, $sortDir);

        if (isset($filters['per_page']) || filter_var($filters['paginate'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $perPage = (int) ($filters['per_page'] ?? 15);

            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Find a single product by ID with its relationships or fail.
     */
    public function findOrFail(int $id): Product
    {
        return Product::with(['currency', 'prices.currency'])->findOrFail($id);
    }

    /**
     * Create a new product within a database transaction.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create($data);

            return $product->load(['currency', 'prices.currency']);
        });
    }

    /**
     * Update an existing product within a database transaction.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update($data);

            return $product->refresh()->load(['currency', 'prices.currency']);
        });
    }

    /**
     * Soft-delete a product within a database transaction.
     */
    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->delete();
        });
    }
}
