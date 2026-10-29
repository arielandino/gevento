<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\StoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly CurrencyService $currencyService
    ) {}

    /**
     * GET /currencies
     * Obtener lista de divisas.
     */
    public function index(): AnonymousResourceCollection
    {
        $currencies = $this->currencyService->list();

        return CurrencyResource::collection($currencies)->additional([
            'success' => true,
            'message' => 'Lista de divisas obtenida correctamente.',
        ]);
    }

    /**
     * POST /currencies
     * Crear una nueva divisa.
     */
    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $currency = $this->currencyService->create($request->validated());

        return (new CurrencyResource($currency))
            ->additional([
                'success' => true,
                'message' => 'Divisa creada correctamente.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /currencies/{id}
     * Obtener una divisa por ID.
     */
    public function show(int $id): CurrencyResource
    {
        $currency = $this->currencyService->findOrFail($id);

        return (new CurrencyResource($currency))->additional([
            'success' => true,
            'message' => 'Divisa obtenida correctamente.',
        ]);
    }

    /**
     * PUT /currencies/{id}
     * Actualizar una divisa.
     */
    public function update(UpdateCurrencyRequest $request, int $id): CurrencyResource
    {
        $currency = $this->currencyService->findOrFail($id);
        $updated = $this->currencyService->update($currency, $request->validated());

        return (new CurrencyResource($updated))->additional([
            'success' => true,
            'message' => 'Divisa actualizada correctamente.',
        ]);
    }

    /**
     * DELETE /currencies/{id}
     * Eliminar una divisa.
     */
    public function destroy(int $id): JsonResponse
    {
        $currency = $this->currencyService->findOrFail($id);

        if ($currency->products()->exists() || $currency->productPrices()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la divisa porque tiene productos o precios asociados.',
            ], 409);
        }

        $this->currencyService->delete($currency);

        return response()->json([
            'success' => true,
            'message' => 'Divisa eliminada correctamente.',
            'data' => null,
        ], 200);
    }
}
