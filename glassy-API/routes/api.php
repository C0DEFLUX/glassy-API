<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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

Route::middleware('VerifyUserToken')->get('/verify-user', [UserController::class, 'userVerify']);

//Login
Route::post('login', [UserController::class, 'login']);

//Image upload
Route::post('add-product', [ProductController::class, 'addProduct']);

Route::get('product-data', [ProductController::class, 'index']);

Route::delete('delete-product/{id}', [ProductController::class, 'removeProduct']);
