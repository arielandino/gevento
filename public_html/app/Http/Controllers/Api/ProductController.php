<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * GET /products
     * Obtener lista de productos.
     */
    public function index(IndexProductRequest $request): AnonymousResourceCollection
    {
        $products = $this->productService->list($request->validated());

        return ProductResource::collection($products)->additional([
            'success' => true,
            'message' => 'Lista de productos obtenida correctamente.',
        ]);
    }

    /**
     * POST /products
     * Crear un nuevo producto.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return (new ProductResource($product))
            ->additional([
                'success' => true,
                'message' => 'Producto creado correctamente.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /products/{id}
     * Obtener un producto por ID.
     */
    public function show(int $id): ProductResource
    {
        $product = $this->productService->findOrFail($id);

        return (new ProductResource($product))->additional([
            'success' => true,
            'message' => 'Producto obtenido correctamente.',
        ]);
    }

    /**
     * PUT /products/{id}
     * Actualizar un producto.
     */
    public function update(UpdateProductRequest $request, int $id): ProductResource
    {
        $product = $this->productService->findOrFail($id);
        $updated = $this->productService->update($product, $request->validated());

        return (new ProductResource($updated))->additional([
            'success' => true,
            'message' => 'Producto actualizado correctamente.',
        ]);
    }

    /**
     * DELETE /products/{id}
     * Eliminar un producto.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->findOrFail($id);
        $this->productService->delete($product);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente.',
            'data' => null,
        ], 200);
    }
}
