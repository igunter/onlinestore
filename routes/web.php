<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::post('categories/reorder', [App\Http\Controllers\CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::resource('categories', App\Http\Controllers\CategoryController::class)->except('show');
});
