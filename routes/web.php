<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('storage-link', function () {
    Artisan::call('storage:link', []);
    return 'success';
});

Route::get('laravel-migration', function () {
    Artisan::call('migrate', []);
    return 'success';
});

Route::get('laravel-seed', function () {
    Artisan::call('db:seed',[]);
    return 'success';
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return 'success';
});

Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
    return 'success';
});

Route::get('/view-clear', function () {
    Artisan::call('view:clear');
    return 'success';
});

Route::get('/route-clear', function () {
    Artisan::call('route:clear');
    return 'success';
});

Route::get('/config-clear', function () {
    Artisan::call('config:cache');
    return 'success';
});
