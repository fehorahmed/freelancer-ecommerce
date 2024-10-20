<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RoomCategoryController;
use App\Http\Controllers\SubDistrictController;
use App\Http\Controllers\UserController;
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
                Route::post('/brand-list', [BrandController::class, 'getBrandList'])->name('products.brands.list.autocomplete');
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
});
