<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
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

Route::post('/register', [UserController::class, "register"]);
Route::post('/login', [UserController::class, "login"]);
//Route::middleware('auth:sanctum')->group(function () {
//    Route::post("/products/create", [ProductController::class, "create"]);
//    Route::post("/products/batchCreate", [ProductController::class, "batchCreate"]);
//});
Route::get("/products/list", [ProductController::class, "list"]);
Route::get("/products/detail", [ProductController::class, "detail"]);
Route::post("/products/batchCreate", [ProductController::class, "create"]);
Route::post('/products/import', [ProductController::class, "import"]);
Route::get('/products/export', [ProductController::class, "export"]);
Route::post("/products/update", [ProductController::class, "update"]);
Route::get("/products/delete", [ProductController::class, "delete"]);
