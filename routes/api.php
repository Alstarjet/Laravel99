<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;


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

Route::middleware('auth:sanctum')->get('/userr', function (Request $request) {
    return $request->user();
});
Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orderfind/{numero}', [OrderController::class, 'show']);

});

Route::get('/saludo', function () {
    return response()->json(['mensaje' => '¡Hola desde Laravel API!']);
});
Route::get('/users', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/order/update', [OrderController::class, 'update']);
Route::post('/order/cancel', [OrderController::class, 'cancel']);






Route::post('login', [AuthController::class, 'login']);
