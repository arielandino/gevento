<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPrice\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Services\ProductPriceService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductPriceController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly ProductPriceService $productPriceService
    ) {}

    /**
     * GET /products/{id}/prices
     * Obtener lista de precios de un producto.
     */
    public function index(int $id): AnonymousResourceCollection
    {
        $product = $this->productService->findOrFail($id);
        $prices = $this->productPriceService->listByProduct($product);

        return ProductPriceResource::collection($prices)->additional([
            'success' => true,
            'message' => 'Lista de precios del producto obtenida correctamente.',
        ]);
    }

    /**
     * POST /products/{id}/prices
     * Crear un nuevo precio para un producto.
     */
    public function store(StoreProductPriceRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->findOrFail($id);
        $price = $this->productPriceService->storeForProduct($product, $request->validated());

        return (new ProductPriceResource($price))
            ->additional([
                'success' => true,
                'message' => 'Precio del producto registrado correctamente.',
            ])
            ->response()
            ->setStatusCode(201);
    }
}
