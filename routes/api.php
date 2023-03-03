<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where I registered API routes for my application.
|
*/

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

// api rest to sell products
// API REST EXAMPLE:
// POST http://127.0.0.1:8000/api/tiendas/10/productos/5?cantidad=1
Route::post('/tiendas/{idTienda}/productos/{idProducto}', function (Request $request, $idTienda, $idProducto) {
    $rules = [
        'cantidad' => 'required|integer',
    ];

    $messages = [
        'cantidad.required' => 'La cantidad es obligatoria',
        'cantidad.integer' => 'La cantidad debe ser un número entero',
    ];

    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Error al vender el producto',
            'errors' => $validator->errors()
        ], 400);
    }

    $tienda = \App\Models\Tienda::find($idTienda);
    if($tienda == null) {
        return response()->json([
            'message' => 'Error al vender el producto',
            'errors' => 'No existe la tienda con id ' . $idTienda
        ], 400);
    }

    $producto = \App\Models\Producto::find($idProducto);
    if($producto == null) {
        return response()->json([
            'message' => 'Error al vender el producto',
            'errors' => 'No existe el producto con id ' . $idProducto
        ], 400);
    }

    $cantidad = $request->cantidad;
    $cantidadActual = $tienda->productos()->where('producto_id', $idProducto)->first()->pivot->cantidad;
    if($cantidad > $cantidadActual) {
        return response()->json([
            'message' => 'Error al vender el producto',
            'errors' => 'No hay suficientes productos en la tienda'
        ], 400);
    }

    $tienda->productos()->updateExistingPivot($idProducto, ['cantidad' => $cantidadActual - $cantidad]);

    $message = 'Producto vendido con éxito';
    if ($tienda->productos()->where('producto_id', $idProducto)->first()->pivot->cantidad < 3) {
        $message .= " ¡Cuidado! Quedan pocos productos en la tienda";
    }

    return response()->json([
        'message' => $message,
        'tienda' => $tienda->load('productos')->toJson(JSON_PRETTY_PRINT),
        'stock' => $tienda->productos()->where('producto_id', $idProducto)->first()->pivot->cantidad,
    ], 201);
});
