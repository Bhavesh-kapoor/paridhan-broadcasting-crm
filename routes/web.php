<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

#admin routes
Route::middleware(['web', 'auth:web'])->prefix('admin')->group(function () {
    Route::get('/dashboard', ["App\Http\Controllers\DashboardController", "index"])->name('dashboard');







    #auth related routes 
    Route::get('/logout', ["App\Http\Controllers\AuthController", "logout"])->name('logout');
});


#auth routes 
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('sign-in', ["App\Http\Controllers\AuthController", 'signin'])->name('login');
    Route::post('sign-in', ["App\Http\Controllers\AuthController", 'validate'])->name('login.validate');
});
