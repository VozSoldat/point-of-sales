<?php

use App\Http\Controllers\FoodBeverageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('category')->group(function () {
    Route::get(
        '/food-beverage',
        [ProductController::class, 'foodBeverage']
    );

    Route::get(
        '/beauty-health',
        [ProductController::class, 'beautyHealth']
    );
    Route::get(
        '/home-care',
        [ProductController::class, 'homeCare']
    );
    Route::get(
        '/baby-kid',
        [ProductController::class, 'babyKid']
    );
});

Route::get('/user/{id}/name/{name}',[UserController::class,'index'])->name('user');

Route::get('/sales', [HomeController::class, 'sales'])->name('sales');

Route::get('/level', [LevelController::class, 'index']);
Route::get('/kategori', [KategoriController::class, 'index']);
