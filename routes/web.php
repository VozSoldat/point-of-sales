<?php

use App\Http\Controllers\FoodBeverageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
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

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route
    ::group(['prefix' => 'user'], function () {
       Route::get('/', [UserController::class, 'index'])->name('user.index');
       Route::post('/list', [UserController::class, 'list'])->name('user.list');
       Route::get('/create', [UserController::class, 'create'])->name('user.create');
       Route::post('/', [UserController::class, 'store'])->name('user.store');
       Route::get('/{id}', [UserController::class, 'show'])->name('user.show');
       Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
       Route::put('/{id}', [UserController::class, 'update'])->name('user.update');
       Route::delete('/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    });
