<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\FoodBeverageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Models\Kategori;
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

Route::pattern('id', '[0-9]+');

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');



Route::middleware(['auth'])->group(function () {
    // masukkan semua route yang perlu autentikasi di sini

    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::middleware(['authorize:ADM'])->group(function () {
        Route
            ::group(['prefix' => 'user'], function () {
                Route::get('/', [UserController::class, 'index'])->name('user.index');
                Route::post('/list', [UserController::class, 'list'])->name('user.list');
                Route::get('/create', [UserController::class, 'create'])->name('user.create');
                Route::post('/', [UserController::class, 'store'])->name('user.store');

                Route::get('/create_ajax', [UserController::class, 'create_ajax'])->name('user.create_ajax');
                Route::post('ajax', [UserController::class, 'store_ajax'])->name('user.ajax');

                Route::get('/{id}', [UserController::class, 'show'])->name('user.show');
                Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
                Route::put('/{id}', [UserController::class, 'update'])->name('user.update');

                Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax'])->name('user.edit_ajax');
                Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax'])->name('user.update_ajax');

                Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);
                Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);

                Route::delete('/{id}', [UserController::class, 'destroy'])->name('user.destroy');
            });
        Route
            ::group(['prefix' => 'level'], function () {
                Route::get('/', [LevelController::class, 'index'])->name('level.index');
                Route::post('/list', [LevelController::class, 'list'])->name('level.list');
                Route::get('/create', [LevelController::class, 'create'])->name('level.create');
                Route::post('/', [LevelController::class, 'store'])->name('level.store');
                Route::get('/create_ajax', [LevelController::class, 'create_ajax'])->name('level.create_ajax');
                Route::post('ajax', [LevelController::class, 'store_ajax'])->name('level.ajax');

                Route::get('/{id}', [LevelController::class, 'show'])->name('level.show');
                Route::get('/{id}/edit', [LevelController::class, 'edit'])->name('level.edit');
                Route::put('/{id}', [LevelController::class, 'update'])->name('level.update');

                Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax'])->name('level.edit_ajax');
                Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax'])->name('level.update_ajax');

                Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
                Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);

                Route::delete('/{id}', [LevelController::class, 'destroy'])->name('user.destroy');
            });

        Route
            ::group(['prefix' => 'kategori'], function () {
                Route::get('/', [KategoriController::class, 'index'])->name('kategori.index');
                Route::post('/list', [KategoriController::class, 'list'])->name('kategori.list');
                Route::get('/create', [KategoriController::class, 'create'])->name('kategori.create');
                Route::post('/', [KategoriController::class, 'store'])->name('kategori.store');
                Route::get('/create_ajax', [KategoriController::class, 'create_ajax'])->name('kategori.create_ajax');
                Route::post('ajax', [KategoriController::class, 'store_ajax'])->name('kategori.ajax');

                Route::get('/{id}', [KategoriController::class, 'show'])->name('kategori.show');
                Route::get('/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
                Route::put('/{id}', [KategoriController::class, 'update'])->name('kategori.update');

                Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax'])->name('kategori.edit_ajax');
                Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax'])->name('kategori.update_ajax');

                Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);
                Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);

                Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
            });
        Route
            ::group(['prefix' => 'supplier'], function () {
                Route::get('/', [SupplierController::class, 'index'])->name('supplier.index');
                Route::post('/list', [SupplierController::class, 'list'])->name('supplier.list');
                Route::get('/create', [SupplierController::class, 'create'])->name('supplier.create');
                Route::post('/', [SupplierController::class, 'store'])->name('supplier.store');
                Route::get('/create_ajax', [SupplierController::class, 'create_ajax'])->name('supplier.create_ajax');
                Route::post('ajax', [SupplierController::class, 'store_ajax'])->name('supplier.ajax');

                Route::get('/{id}', [SupplierController::class, 'show'])->name('supplier.show');
                Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
                Route::put('/{id}', [SupplierController::class, 'update'])->name('supplier.update');

                Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax'])->name('supplier.edit_ajax');
                Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax'])->name('supplier.update_ajax');

                Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);
                Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);

                Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
            });
        Route
            ::group(['prefix' => 'barang'], function () {
                Route::get('/', [BarangController::class, 'index'])->name('barang.index');
                Route::post('/list', [BarangController::class, 'list'])->name('barang.list');
                Route::get('/create', [BarangController::class, 'create'])->name('barang.create');
                Route::post('/', [BarangController::class, 'store'])->name('barang.store');
                Route::get('/create_ajax', [BarangController::class, 'create_ajax'])->name('barang.create_ajax');
                Route::post('ajax', [BarangController::class, 'store_ajax'])->name('barang.ajax');

                Route::get('/{id}', [BarangController::class, 'show'])->name('barang.show');
                Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
                Route::put('/{id}', [BarangController::class, 'update'])->name('barang.update');

                Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax'])->name('barang.edit_ajax');
                Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax'])->name('barang.update_ajax');

                Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
                Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);

                Route::delete('/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
            });
    });
});
