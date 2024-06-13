<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MarketingController;
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

Route::get('register', [UserController::class, 'register']);

Route::get('users', [UserController::class, 'index']);

Route::get('test-user', [UserController::class, 'testUser']);

//Categories
Route::get('category-data', [CategoryController::class, 'index']);

Route::middleware('VerifyUserToken')->post('add-category', [CategoryController::class, 'create']);

Route::middleware('VerifyUserToken')->post('edit-category/{id}', [CategoryController::class, 'update']);

Route::middleware('VerifyUserToken')->delete('delete-category/{id}', [CategoryController::class, 'destroy']);

//Products
Route::middleware('VerifyUserToken')->post('add-product', [ProductController::class, 'create']);

Route::middleware('VerifyUserToken')->post('edit-product/{id}', [ProductController::class, 'update']);

Route::get('product-data', [ProductController::class, 'index']);

Route::middleware('VerifyUserToken')->delete('delete-product/{id}', [ProductController::class, 'destroy']);

Route::get('product-by-id/{id}', [ProductController::class, 'getById']);

Route::get('gallery-data', [ProductController::class, 'galleryIndex']);

Route::get('products-by-category/{categoryId}', [ProductController::class, 'getByCategoryId']);

//Marketing
Route::get('title-image', [MarketingController::class, 'index']);

Route::middleware('VerifyUserToken')->post('add-title-image', [MarketingController::class, 'create']);
