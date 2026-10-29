<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'service' => config('app.name', 'Gevento Products API'),
        'version' => '1.0.0',
        'documentation' => [
            'swagger_ui' => url('/swagger/index.html'),
            'openapi_json' => url('/swagger/openapi.json'),
            'openapi_yaml' => url('/swagger/openapi.yaml'),
            'postman_collection' => url('/swagger/postman_collection.json'),
            'insomnia_collection' => url('/swagger/insomnia_collection.json'),
            'documento_tecnico' => url('/swagger/DOCUMENTACION_API.md'),
        ],
        'endpoints' => [
            'GET /products' => 'Obtener lista de productos',
            'POST /products' => 'Crear un nuevo producto',
            'GET /products/{id}' => 'Obtener un producto por ID',
            'PUT /products/{id}' => 'Actualizar un producto',
            'DELETE /products/{id}' => 'Eliminar un producto',
            'GET /products/{id}/prices' => 'Obtener lista de precios de un producto',
            'POST /products/{id}/prices' => 'Crear un nuevo precio para un producto',
            'GET /currencies' => 'Obtener lista de divisas',
            'POST /currencies' => 'Crear una nueva divisa',
        ],
    ]);
});

Route::get('/api/documentation', function () {
    return redirect('/swagger/index.html');
});
