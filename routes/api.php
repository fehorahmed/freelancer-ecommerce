<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RoomCategoryController;
use App\Http\Controllers\SubDistrictController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarrantyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/user-info', function (Request $request) {
    dd($request->user());
    return $request->user();
});

/*******************************
Admin API
 ******************************* */

Route::post('admin/login', [AdminController::class, 'apiLogin']);

Route::middleware('auth:sanctum', 'ability:admin', 'throttle:1000,1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('profile', [AdminController::class, 'profile']);

        Route::group(['prefix' => 'products'], function () {
            Route::group(['prefix' => 'brands'], function () {
                Route::get('/', [BrandController::class, 'index'])->name('products.brands.index');
                Route::post('/create', [BrandController::class, 'store'])->name('products.brands.store');
                Route::get('/{id}/edit', [BrandController::class, 'edit'])->name('products.brands.edit');
                Route::post('/{id}/edit', [BrandController::class, 'update'])->name('products.brands.update');
                Route::delete('/{id}/delete', [BrandController::class, 'destroy'])->name('products.brands.delete');
            });
            Route::group(['prefix' => 'units'], function () {
                Route::get('/', [UnitController::class, 'index'])->name('products.units.index');
                Route::post('/create', [UnitController::class, 'store'])->name('products.units.store');
                Route::get('/{id}/edit', [UnitController::class, 'edit'])->name('products.units.edit');
                Route::post('/{id}/edit', [UnitController::class, 'update'])->name('products.units.update');
                Route::delete('/{id}/delete', [UnitController::class, 'destroy'])->name('products.units.delete');
            });
            Route::group(['prefix' => 'categories'], function () {
                Route::get('/', [CategoryController::class, 'index'])->name('products.categories.index');
                Route::post('/create', [CategoryController::class, 'store'])->name('products.categories.store');
                Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('products.categories.edit');
                Route::post('/{id}/edit', [CategoryController::class, 'update'])->name('products.categories.update');
                Route::delete('/{id}/delete', [CategoryController::class, 'destroy'])->name('products.categories.delete');
            });
            Route::group(['prefix' => 'warranties'], function () {
                Route::get('/', [WarrantyController::class, 'index'])->name('products.warranties.index');
                Route::post('/create', [WarrantyController::class, 'store'])->name('products.warranties.store');
                Route::get('/{id}/edit', [WarrantyController::class, 'edit'])->name('products.warranties.edit');
                Route::post('/{id}/edit', [WarrantyController::class, 'update'])->name('products.warranties.update');
                Route::delete('/{id}/delete', [WarrantyController::class, 'destroy'])->name('products.warranties.delete');
            });
        });
    });
});



/*******************************
User API
 ******************************* */
Route::post('login', [UserController::class, 'apiLogin']);
Route::post('registration', [UserController::class, 'apiRegistration']);

Route::middleware('auth:sanctum', 'ability:user', 'throttle:1000,1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('profile', [AdminController::class, 'profile']);
    });
});

/*******************************
Frontend API
 ******************************* */

/*******************************
Common API
 ******************************* */
Route::middleware('auth:sanctum', 'throttle:1000,1')->group(function () {

    Route::get('profile', [CommonController::class, 'profile']);
});
Route::prefix('common')->middleware('throttle:1000,1')->group(function () {
    Route::get('get-division', [DivisionController::class, 'apiGetDivision']);
    Route::get('get-district', [DistrictController::class, 'apiGetDistrict']);
    Route::get('get-sub-district', [SubDistrictController::class, 'apiGetSubDistrict']);


    Route::get('get-active-categories', [CategoryController::class, 'getActiveCategories']);
    Route::get('get-all-categories', [CategoryController::class, 'getAllCategories']);

});
