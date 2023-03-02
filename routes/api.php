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
    return \App\Models\Tienda::all()->load('productos');
});

Route::get('/tiendas/{id}', function ($id) {
    return \App\Models\Tienda::find($id);
});

Route::get('/productos', function () {
    return \App\Models\Producto::all();
});
