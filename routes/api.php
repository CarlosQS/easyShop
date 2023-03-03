<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tiendas', function () {
    return \App\Models\Tienda::all()->load('productos')->toJson(JSON_PRETTY_PRINT);
});

Route::get('/tiendas/{id}', function ($id) {
    $rules = [
        'id' => 'required|integer',
    ];

    $messages = [
        'id.required' => 'El id de la tienda es obligatorio',
        'id.integer' => 'El id de la tienda debe ser un número entero',
    ];

    $validator = \Illuminate\Support\Facades\Validator::make(['id' => $id], $rules, $messages);
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Error al obtener la tienda',
            'errors' => $validator->errors()
        ], 400);
    }

    $tienda = \App\Models\Tienda::find($id);
    if($tienda == null) {
        return response()->json([
            'message' => 'Error al obtener la tienda',
            'errors' => 'No existe la tienda con id ' . $id
        ], 400);
    }

    return $tienda->load('productos')->toJson(JSON_PRETTY_PRINT);
});

route::get('/productos', function () {
    return \App\Models\Producto::all();
});


// API REST EXAMPLE:
// POST http://127.0.0.1:8000/api/tiendas/?nombre=NewShop&productos=[{"id":1,"cantidad":4},{"id":2,"cantidad":2},{"id":3,"cantidad":5}]
Route::post('/tiendas', function (Request $request) {
    $rules = [
        'nombre' => 'required',
    ];

    $messages = [
        'nombre.required' => 'El nombre de la tienda es obligatorio',
    ];

    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Error al crear la tienda',
            'errors' => $validator->errors()
        ], 400);
    }

    $tienda = new \App\Models\Tienda();
    $tienda->nombre = $request->nombre;
    $tienda->save();

    if($request->productos != null) {
        $productos = $request->productos;
        if (!is_string($productos) || !is_array(json_decode($productos, true)) || (json_last_error() !== JSON_ERROR_NONE)) {
            $tienda->delete();
            return response()->json([
                'message' => 'Error al crear la tienda',
                'errors' => 'El campo productos no es un json válido'
            ], 400);
        }

        $productosJson = json_decode($productos, true);

        foreach ($productosJson as $producto) {
            $tienda->productos()->attach($producto['id'], ['cantidad' => $producto['cantidad']]);
        }
    }

    return response()->json([
        'message' => 'Tienda creada con éxito',
        'tienda' => $tienda->load('productos')->toJson(JSON_PRETTY_PRINT)
    ], 201);
});

// API REST EXAMPLE:
// PUT http://127.0.0.1:8000/api/tiendas/15/?nombre=editedShop
Route::put('/tiendas/{id}', function (Request $request, $id) {
    $rules = [
        'nombre' => 'required',
    ];

    $messages = [
        'nombre.required' => 'El nombre de la tienda es obligatorio',
    ];

    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Error al editar la tienda',
            'errors' => $validator->errors()
        ], 400);
    }

    $tienda = \App\Models\Tienda::find($id);
    if($tienda == null) {
        return response()->json([
            'message' => 'Error al editar la tienda',
            'errors' => 'No existe la tienda con id ' . $id
        ], 400);
    }

    $tienda->nombre = $request->nombre;
    $tienda->save();

    return response()->json([
        'message' => 'Tienda editada con éxito',
        'tienda' => $tienda->toJson(JSON_PRETTY_PRINT)
    ], 201);
});

// API REST EXAMPLE:
// DELETE http://127.0.0.1:8000/api/tiendas/15
Route::delete('/tiendas/{id}', function ($id) {
    $rules = [
        'id' => 'required|integer',
    ];

    $messages = [
        'id.required' => 'El id de la tienda es obligatorio',
        'id.integer' => 'El id de la tienda debe ser un número entero',
    ];

    $validator = \Illuminate\Support\Facades\Validator::make(['id' => $id], $rules, $messages);
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Error al eliminar la tienda',
            'errors' => $validator->errors()
        ], 400);
    }

    $tienda = \App\Models\Tienda::find($id);
    if($tienda == null) {
        return response()->json([
            'message' => 'Error al eliminar la tienda',
            'errors' => 'No existe la tienda con id ' . $id
        ], 400);
    }

    $tienda->productos()->detach();
    $tienda->delete();

    return response()->json([
        'message' => 'Tienda eliminada con éxito',
    ], 201);
});
