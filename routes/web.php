<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

#admin routes
Route::middleware(['web', 'auth:web'])->prefix('admin')->group(function () {
    Route::get('/dashboard', ["App\Http\Controllers\DashboardController", "index"])->name('dashboard');

    # Employee routes
    Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
    Route::get('employees/{employee}/change-password', [\App\Http\Controllers\EmployeeController::class, 'showChangePassword'])->name('employees.change-password');
    Route::post('employees/{employee}/change-password', [\App\Http\Controllers\EmployeeController::class, 'changePassword'])->name('employees.change-password.store');
    Route::post('employees/{employee}/toggle-status', [\App\Http\Controllers\EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

    # Contact routes (Exhibitors & Visitors)
    Route::resource('contacts', \App\Http\Controllers\ContactController::class);

    # Campaign routes
    Route::resource('campaigns', \App\Http\Controllers\CampaignController::class);
    Route::post('campaigns/{campaign}/send', [\App\Http\Controllers\CampaignController::class, 'send'])->name('campaigns.send');
    Route::get('campaigns/contacts/get', [\App\Http\Controllers\CampaignController::class, 'getContacts'])->name('campaigns.contacts.get');


    # Admin change password
    Route::get('/change-password', [\App\Http\Controllers\AuthController::class, 'showChangePassword'])->name('admin.change-password');
    Route::post('/change-password', [\App\Http\Controllers\AuthController::class, 'changePassword'])->name('admin.change-password.store');

    #auth related routes
    Route::get('/logout', ["App\Http\Controllers\AuthController", "logout"])->name('logout');

    # Location routes
    Route::resource('locations', \App\Http\Controllers\LocationController::class);
    Route::get('/get/locations/datatable', [\App\Http\Controllers\LocationController::class, 'getLocations'])->name('get.locations.data');

    Route::resource('leads', \App\Http\Controllers\LeadController::class);
    Route::get('/get/leads/datatable', [\App\Http\Controllers\LeadController::class, 'getLeads'])->name('get.leads.data');
    Route::post('/follow-up/store', [App\Http\Controllers\LeadController::class, 'store'])
        ->name('followup.store');
    Route::get('/get-followups/{phone}', [App\Http\Controllers\LeadController::class, 'getFollowUps'])->name('get.followups');
});


#auth routes
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('sign-in', ["App\Http\Controllers\AuthController", 'signin'])->name('login');
    Route::post('sign-in', ["App\Http\Controllers\AuthController", 'validate'])->name('login.validate');



    // Lead routes
});
