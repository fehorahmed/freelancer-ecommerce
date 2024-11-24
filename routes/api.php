<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\GlobalConfigController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RoomCategoryController;
use App\Http\Controllers\ShippingChargeController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SubDistrictController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
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

                Route::get('get-all', [CategoryController::class, 'getAllCategoryForAdmin']);
            });
            Route::group(['prefix' => 'warranties'], function () {
                Route::get('/', [WarrantyController::class, 'index'])->name('products.warranties.index');
                Route::post('/create', [WarrantyController::class, 'store'])->name('products.warranties.store');
                Route::get('/{id}/edit', [WarrantyController::class, 'edit'])->name('products.warranties.edit');
                Route::post('/{id}/edit', [WarrantyController::class, 'update'])->name('products.warranties.update');
                Route::delete('/{id}/delete', [WarrantyController::class, 'destroy'])->name('products.warranties.delete');
            });
            Route::group(['prefix' => 'product'], function () {
                Route::get('/', [ProductController::class, 'index'])->name('products.product.index');
                Route::post('/create', [ProductController::class, 'store'])->name('products.product.store');
                Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('products.product.edit');
                Route::post('/{id}/edit', [ProductController::class, 'update'])->name('products.product.update');
                Route::post('/stock-edit', [ProductController::class, 'stockEditByProduct'])->name('products.product.stock-edit');

                // Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('products.product.delete');
                // Route::post('/products-list', [ProductController::class, 'getProductsList'])->name('products.product.list');
                Route::get('/{id}/active', [ProductController::class, 'productActivate'])->name('products.product.active');
                Route::get('/{id}/deactive', [ProductController::class, 'productDeActivate'])->name('products.product.deactive');

                Route::post('/stock-update', [ProductsInventoriesController::class, 'ajaxStockEdit'])->name('products.stock.update');

                //CK upload
                Route::post('/ckeditor-upload', [ProductController::class, 'ckeditorUpload'])->name('ckeditor.upload');
            });
        });
        Route::group(['prefix' => 'campain'], function () {
            Route::group(['prefix' => 'vouchers'], function () {
                Route::get('/', [VoucherController::class, 'index'])->name('campain.vouchers.index');
                Route::post('/create', [VoucherController::class, 'store'])->name('campain.vouchers.store');
                Route::get('/{id}/edit', [VoucherController::class, 'edit'])->name('campain.vouchers.edit');
                Route::post('/{id}/edit', [VoucherController::class, 'update'])->name('campain.vouchers.update');
                Route::delete('/{id}/delete', [VoucherController::class, 'destroy'])->name('campain.vouchers.delete');
            });
        });

        Route::group(['prefix' => 'shipping-charges'], function () {
            Route::get('/', [ShippingChargeController::class, 'index'])->name('shipping.charges.index');
            Route::get('/create', [ShippingChargeController::class, 'create'])->name('shipping.charges.create');
            Route::post('/create', [ShippingChargeController::class, 'store'])->name('shipping.charges.store');
            Route::get('/{id}/edit',[ShippingChargeController::class, 'edit'])->name('shipping.charges.edit');
            Route::post('/{id}/edit',[ShippingChargeController::class, 'update'])->name('shipping.charges.update');
            Route::delete('/{id}/delete', [ShippingChargeController::class, 'destroy'])->name('shipping.charges.delete');
            Route::delete('/list', [ShippingChargeController::class, 'chargeList'])->name('shipping.charges.list');
        });

        Route::group(['prefix' => 'orders'], function () {
            Route::get('/', [OrderController::class, 'index'])->name('products.orders.index');
            Route::get('/{id}/view', [OrderController::class, 'show'])->name('products.orders.view');
            Route::post('/{id}/status-change', [OrderController::class, 'statusChange'])->name('products.orders.status-change');
            Route::get('/get-all-status', [OrderController::class, 'allStatus'])->name('products.orders.all-status');

            // Route::get('/{id}/edit', [WarrantyController::class, 'edit'])->name('products.orders.edit');
            // Route::post('/{id}/edit', [WarrantyController::class, 'update'])->name('products.orders.update');
            // Route::delete('/{id}/delete', [WarrantyController::class, 'destroy'])->name('products.orders.delete');

            Route::post('/create', [OrderController::class, 'customerOrderStore'])->name('products.orders.customer-order-store');

        });
        Route::group(['prefix' => 'suppliers'], function () {
            Route::get('/', [SupplierController::class, 'index'])->name('products.suppliers.index');
            Route::post('/create', [SupplierController::class, 'store'])->name('products.suppliers.store');
            Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('products.suppliers.edit');
            Route::post('/{id}/edit', [SupplierController::class, 'update'])->name('products.suppliers.update');
            Route::delete('/{id}/delete', [SupplierController::class, 'destroy'])->name('products.suppliers.delete');
        });
        Route::group(['prefix' => 'purchase-order'], function () {
            Route::get('/', [PurchaseOrderController::class, 'index'])->name('products.purchase-order.index');
            Route::post('/create', [PurchaseOrderController::class, 'store'])->name('products.purchase-order.store');
            Route::get('/{id}/edit', [PurchaseOrderController::class, 'edit'])->name('products.purchase-order.edit');
            Route::post('/{id}/edit', [PurchaseOrderController::class, 'update'])->name('products.purchase-order.update');
            // Route::delete('/{id}/delete', [PurchaseOrderController::class, 'destroy'])->name('products.purchase-order.delete');
        });
        Route::group(['prefix' => 'sliders'], function () {
            Route::get('/', [SliderController::class, 'index'])->name('sliders.index');
            Route::post('/create', [SliderController::class, 'store'])->name('sliders.store');
            Route::get('/{id}/edit', [SliderController::class, 'edit'])->name('sliders.edit');
            Route::post('/{id}/edit', [SliderController::class, 'update'])->name('sliders.update');
            Route::get('/{id}/delete', [SliderController::class, 'destroy'])->name('sliders.delete');
        });

        Route::group(['prefix' => 'global-config'], function () {
            Route::get('/', [GlobalConfigController::class, 'index'])->name('global-config.index');
            Route::post('/create', [GlobalConfigController::class, 'store'])->name('global-config.store');
        });
        Route::group(['prefix' => 'pages'], function () {
            Route::get('/', [PageController::class, 'index'])->name('pages.index');
            Route::post('/create', [PageController::class, 'store'])->name('pages.store');
            Route::get('/{id}/edit', [PageController::class, 'edit'])->name('pages.edit');
            Route::post('/{id}/edit', [PageController::class, 'update'])->name('pages.update');
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
        Route::post('save-address', [UserAddressController::class, 'store'])->name('api.address.save');
        Route::get('get-address', [UserAddressController::class, 'getAllAddress'])->name('api.address.all');
        Route::get('{id}/edit-address', [UserAddressController::class, 'editAddress'])->name('api.address.edit');
        Route::post('{id}/update-address', [UserAddressController::class, 'updateAddress'])->name('api.address.update');
        Route::post('delete-address', [UserAddressController::class, 'deleteAddress'])->name('api.address.delete');
        Route::post('place-order', [OrderController::class, 'postOrder'])->name('api.order.place');
        Route::get('orders-history', [OrderController::class, 'getOrderHistory'])->name('api.order.history');
        Route::post('change-password', [UserController::class, 'apiChangePassword'])->name('api.change.password');
    });
});

/*******************************
Frontend API
 ******************************* */

Route::group(['middleware' => 'throttle:999000,1'], function () {
    Route::get('global-config', [GlobalConfigController::class, 'getGlobalConfigForWeb'])->name('api.global-config.web');
    Route::get('all-products', [ProductController::class, 'getAllProductsForWeb'])->name('api.all.products.web');
    Route::post('product-single', [ProductController::class, 'getProductByUrl'])->name('api.single.products');
    Route::post('category-products', [ProductController::class, 'getProductsByCategory'])->name('api.category.products');
    Route::post('brand-products', [ProductController::class, 'getProductsByBrand'])->name('api.brand.products');


    Route::post('forget-password', [UserController::class, 'apiForgetPassword'])->name('api.forget.password');
    Route::post('reset-password', [UserController::class, 'apiResetPassword'])->name('api.reset.password');




});



/*******************************
Common API
 ******************************* */
Route::middleware('auth:sanctum', 'throttle:1000,1')->group(function () {

    Route::get('profile', [CommonController::class, 'profile']);
    Route::get('logout', [CommonController::class, 'logout']);
});
Route::prefix('common')->middleware('throttle:1000,1')->group(function () {
    Route::get('get-division', [DivisionController::class, 'apiGetDivision']);
    Route::get('get-district', [DistrictController::class, 'apiGetDistrict']);
    Route::get('get-sub-district', [SubDistrictController::class, 'apiGetSubDistrict']);


    Route::get('get-all-categories', [CategoryController::class, 'getAllCategories']);
    Route::get('get-active-brands', [BrandController::class, 'getActiveBrands']);
    Route::get('get-active-units', [UnitController::class, 'getActiveUnits']);
    Route::get('get-active-warranty', [WarrantyController::class, 'getActiveWarranty']);
    //Get All Slider
    Route::get('get-all-slider', [SliderController::class, 'getAllSlider']);
    //Get Product Stock
    Route::get('product/{id}/stock', [ProductController::class, 'getStockByProduct'])->name('products.product.get-stock');
    //Coupon Details
    Route::post('coupon-details', [VoucherController::class, 'getCouponDetails']);

    Route::get('products-voucher/{product_id}/{coupon_code}', [VoucherController::class, 'getVoucherProducts'])->name('api.voucher.products');

});
