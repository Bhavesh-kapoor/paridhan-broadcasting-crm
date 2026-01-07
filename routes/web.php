<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProcessLargeFileController;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

#admin routes
Route::middleware(['web', 'auth:web', 'checkRole:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', ["App\Http\Controllers\DashboardController", "index"])->name('admin.dashboard');

    # Employee routes
    Route::resource('employees', EmployeeController::class);
    Route::post('/ajax/get/all-employees', [EmployeeController::class, 'getAllEmployeesList']);
    Route::get('employees/{employee}/change-password', [EmployeeController::class, 'showChangePassword'])->name('employees.change-password');
    Route::post('employees/{employee}/change-password', [EmployeeController::class, 'changePassword'])->name('employees.change-password.store');
    Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

    # Contact routes (Exhibitors & Visitors)
    Route::resource('contacts', ContactController::class);
    Route::post('/ajax/get/all-contacts', [ContactController::class, 'getAllContactsList']);

    # Campaign routes
    Route::resource('campaigns', CampaignController::class);
    Route::post('/ajax/get/all-campaigns', [CampaignController::class, 'getAllCampaignsList']);
    Route::post('/ajax/get/all-campaigns-recipients', [CampaignController::class, 'getAllCampaignsRecipientsList']);
    Route::GET('campaigns/{id}/progress',  [CampaignController::class, 'progress']);

    Route::get('/ajax/exhibitors', [CampaignController::class, 'ajaxExhibitors'])->name('ajax.exhibitors');
    Route::get('/ajax/visitors', [CampaignController::class, 'ajaxVisitors'])->name('ajax.visitors');
    Route::get('/ajax/exhibitors/all', [CampaignController::class, 'getAllExhibitorIDs'])
        ->name('ajax.exhibitors.all');
    Route::post('/campaigns/add-recipients', [CampaignController::class, 'addRecipients'])
        ->name('campaigns.addRecipients');

    Route::get('/ajax/visitors/all', [CampaignController::class, 'getAllVisitorIDs'])
        ->name('ajax.visitors.all');

    Route::post('campaigns/{campaign}/send', [\App\Http\Controllers\CampaignController::class, 'send'])->name('campaigns.send');
    Route::get('campaigns/contacts/get', [\App\Http\Controllers\CampaignController::class, 'getContacts'])->name('campaigns.contacts.get');


    # Admin change password
    Route::get('/change-password', [\App\Http\Controllers\AuthController::class, 'showChangePassword'])->name('admin.change-password');
    Route::post('/change-password', [\App\Http\Controllers\AuthController::class, 'changePassword'])->name('admin.change-password.store');



    #Bulk import (Exhibitors & Visitors) data
    Route::post('/import-contact', [ProcessLargeFileController::class, 'upload'])->name('contact.import');

    // location routes
    Route::resource('locations', LocationController::class);
    Route::post('/ajax/get/all-locations', [LocationController::class, 'getAllLocationsList']);

    // WhatsApp Template routes
    Route::resource('whatsapp-templates', \App\Http\Controllers\WhatsAppTemplateController::class);
    Route::post('/ajax/get/all-whatsapp-templates', [\App\Http\Controllers\WhatsAppTemplateController::class, 'getAllTemplatesList']);
    Route::post('/whatsapp-templates/sync', [\App\Http\Controllers\WhatsAppTemplateController::class, 'sync'])->name('whatsapp-templates.sync');
    Route::get('/whatsapp-templates/{id}/details', [\App\Http\Controllers\WhatsAppTemplateController::class, 'getTemplateDetails'])->name('whatsapp-templates.details');
    Route::get('/ajax/approved-templates', [\App\Http\Controllers\WhatsAppTemplateController::class, 'getApprovedTemplates'])->name('whatsapp-templates.approved');
});


// employee routes
Route::prefix('employee')->middleware(['web', 'auth:web', 'checkRole:employee'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('employee.dashboard');

    // Lead routes
    Route::resource('leads', LeadController::class);
    Route::post('/ajax/get/all-leads', [LeadController::class, 'getAllLeadsList']);

    // Booking routes
    Route::post('/booking/check-availability', [LeadController::class, 'checkAvailability'])
        ->name('booking.checkAvailability');
    // get tables by location id
    Route::get('/get-tables/{locationId}', [LeadController::class, 'getTables'])->name('booking.getTables');
    // get price by table id
    Route::get('/get-price/{tableId}', [LeadController::class, 'getPrice'])
        ->name('booking.getPrice');
    // search location
    // Route::get('/api/search-location', [LeadController::class, 'searchLocation'])->name('booking.searchLocation');
});



Route::middleware(['web', 'auth:web', 'checkRole:admin,employee'])->group(function () {
    #auth related routes
    Route::get('/logout', ["App\Http\Controllers\AuthController", "logout"])->name('logout');

     Route::post('/change-password', [\App\Http\Controllers\AuthController::class, 'changePassword'])->name('admin.change-password.store');
     Route::post('/change-profile', [\App\Http\Controllers\AuthController::class, 'changeProfile'])->name('admin.change-profile.store');
});



#auth routes
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('sign-in', ["App\Http\Controllers\AuthController", 'signin'])->name('login');
    Route::post('sign-in', ["App\Http\Controllers\AuthController", 'validate'])->name('login.validate');

    // Lead routes
    Route::post('check-login', ["App\Http\Controllers\AuthController", 'validate'])->name('login.validate');
});
