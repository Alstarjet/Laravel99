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
    //Crear una orden
    Route::post('/order', [OrderController::class, 'store']);
    //Optiene la lista de ordenes
    Route::get('/orders', [OrderController::class, 'index']);
    //Optiene una orden por su ID
    Route::get('/order/{numero}', [OrderController::class, 'show']);
    //Actualiza el estado de una orden
    Route::post('/order/update', [OrderController::class, 'update']);
    //Cancela una orden
    Route::post('/order/cancel', [OrderController::class, 'cancel']);
    //optienes una lista de usuarios
    Route::get('/users', [UserController::class, 'index']);
});

Route::post('/user', [UserController::class, 'store']);






Route::post('login', [AuthController::class, 'login']);
