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
    return \App\Models\Tienda::all()->load('productos')->toJson(JSON_PRETTY_PRINT);;
});

Route::get('/tiendas/{id}', function ($id) {
    return \App\Models\Tienda::find($id)->load('productos')->toJson(JSON_PRETTY_PRINT);;
});

route::get('/productos', function () {
    return \App\Models\Producto::all();
});

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
