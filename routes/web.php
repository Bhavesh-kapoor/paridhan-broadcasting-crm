<?php

use Illuminate\Support\Facades\Route;

#admin routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', fn() => view('"212121'));
});


#auth routes 
Route::get('sign-in', ["App\Http\Controllers\AuthController", 'signin'])->name('login');
Route::post('sign-in', ["App\Http\Controllers\AuthController", 'validate'])->name('login.validate');
