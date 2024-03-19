<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TaskController;
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
// Auth Routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});

// Routes accessed only by logged users
Route::group(['middleware' => 'auth:api'], function () {
    // Products routes
    Route::resource('products', ProductController::class)->only(['store', 'update', 'destroy']);
    // Tasks routes
    Route::resource('tasks', TaskController::class)->only(['store', 'update', 'destroy']);
    // Articles routes
    Route::resource('articles', ArticleController::class)->only(['store', 'update', 'destroy']);
});

// Categories routes
Route::resource('categories', CategoryController::class)->except(['create', 'edit']);

// Products routes
Route::resource('products', ProductController::class)->only(['index', 'show']);
// Tasks routes
Route::resource('tasks', TaskController::class)->only(['index', 'show']);
// Articles routes
Route::resource('articles', ArticleController::class)->only(['index', 'show']);
