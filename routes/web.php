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
    Route::post('campaigns/{campaign}/resend', [\App\Http\Controllers\CampaignController::class, 'resend'])->name('campaigns.resend');
    Route::get('campaigns/contacts/get', [\App\Http\Controllers\CampaignController::class, 'getContacts'])->name('campaigns.contacts.get');


    # Admin change password
    Route::get('/change-password', [\App\Http\Controllers\AuthController::class, 'showChangePassword'])->name('admin.change-password');
    Route::post('/change-password', [\App\Http\Controllers\AuthController::class, 'changePassword'])->name('admin.change-password.store');



    #Bulk import (Exhibitors & Visitors) data
    Route::post('/import-contact', [ProcessLargeFileController::class, 'upload'])->name('contact.import');

    // location routes
    Route::resource('locations', LocationController::class);
    Route::post('/ajax/get/all-locations', [LocationController::class, 'getAllLocationsList']);

    // Template routes
    Route::resource('templates', \App\Http\Controllers\TemplateController::class);
    Route::get('templates-fetch', [\App\Http\Controllers\TemplateController::class, 'getTemplates'])->name('templates.fetch');
    Route::get('templates-refresh', [\App\Http\Controllers\TemplateController::class, 'refreshCache'])->name('templates.refresh');

    // Company Dashboard routes
    Route::get('companies/{id}/dashboard', [\App\Http\Controllers\CompanyController::class, 'dashboard'])->name('companies.dashboard');
    Route::get('companies/{id}/conversations', [\App\Http\Controllers\CompanyController::class, 'getConversations'])->name('companies.conversations');
    
    // Campaign Analytics route
    Route::get('campaigns/{id}/analytics', [\App\Http\Controllers\CampaignController::class, 'analytics'])->name('campaigns.analytics');
    
    // Dashboard Revenue Filter route
    Route::post('/dashboard/revenue', [\App\Http\Controllers\DashboardController::class, 'getRevenueData'])->name('admin.dashboard.revenue');
    
    // Conversation Management routes (Admin only)
    Route::resource('conversations', \App\Http\Controllers\ConversationController::class);
    Route::post('/ajax/get/all-conversations', [\App\Http\Controllers\ConversationController::class, 'getAllConversationsList'])->name('conversations.list');
    Route::get('/conversations/location/{locationId}/tables', [\App\Http\Controllers\ConversationController::class, 'getTables'])->name('conversations.getTables');
    
    // Invoice Management routes
    Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/ajax/get/all-invoices', [\App\Http\Controllers\InvoiceController::class, 'getAllInvoices'])->name('invoices.list');
    Route::get('/bookings/{bookingId}/invoice', [\App\Http\Controllers\InvoiceController::class, 'generate'])->name('bookings.invoice');
    Route::get('/conversations/{conversationId}/invoice', [\App\Http\Controllers\InvoiceController::class, 'generateFromConversation'])->name('conversations.invoice');
    
    // Admin Bookings routes
    Route::get('/bookings', [\App\Http\Controllers\InvoiceController::class, 'adminBookings'])->name('admin.bookings.index');
    Route::post('/bookings/list', [\App\Http\Controllers\InvoiceController::class, 'getAdminBookings'])->name('admin.bookings.list');
    Route::post('/bookings/settle', [\App\Http\Controllers\InvoiceController::class, 'settleAdminBookingAmount'])->name('admin.bookings.settle');
    Route::get('/bookings/{bookingId}/payment-history', [\App\Http\Controllers\InvoiceController::class, 'getPaymentHistory'])->name('admin.bookings.payment-history');
    Route::post('/bookings/{bookingId}/release', [\App\Http\Controllers\InvoiceController::class, 'releaseTable'])->name('admin.bookings.release');
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
    // search contact by phone
    Route::post('/contacts/search-by-phone', [LeadController::class, 'searchContactByPhone'])
        ->name('contacts.searchByPhone');
    
    // Table Availability routes
    Route::get('/table-availability', [\App\Http\Controllers\TableAvailabilityController::class, 'index'])->name('table-availability.index');
    Route::get('/table-availability/search', [\App\Http\Controllers\TableAvailabilityController::class, 'searchLocations'])->name('table-availability.search-locations');
    Route::get('/table-availability/location/{locationId}', [\App\Http\Controllers\TableAvailabilityController::class, 'getLocationTables'])->name('table-availability.location');
    Route::post('/table-availability/conversation', [\App\Http\Controllers\TableAvailabilityController::class, 'createConversation'])->name('table-availability.conversation');
    Route::post('/table-availability/booking', [\App\Http\Controllers\TableAvailabilityController::class, 'createBooking'])->name('table-availability.booking');
    
    // Campaign Management routes for employees
    Route::get('/campaigns', [\App\Http\Controllers\CampaignController::class, 'employeeIndex'])->name('employee.campaigns.index');
    Route::get('/campaigns/{campaignId}/recipients', [\App\Http\Controllers\CampaignController::class, 'recipients'])->name('employee.campaigns.recipients');
    Route::post('/campaigns/{campaignId}/recipients/list', [\App\Http\Controllers\CampaignController::class, 'getRecipientsList'])->name('employee.campaigns.recipients.list');
    
    // Campaign Conversations routes
    Route::get('/campaigns/{campaignId}/conversations', [\App\Http\Controllers\CampaignController::class, 'conversations'])->name('campaigns.conversations');
    Route::post('/campaigns/{campaignId}/conversations', [\App\Http\Controllers\CampaignController::class, 'storeConversation'])->name('campaigns.conversations.store');
    Route::get('/campaigns/{campaignId}/conversations/create', [\App\Http\Controllers\CampaignController::class, 'createConversation'])->name('campaigns.conversations.create');
    Route::post('/campaigns/{campaignId}/conversations/visitor', [\App\Http\Controllers\CampaignController::class, 'getVisitorConversations'])->name('campaigns.conversations.visitor');
    
    // Invoice routes for employees
    Route::get('/conversations/{conversationId}/invoice', [\App\Http\Controllers\InvoiceController::class, 'generateFromConversation'])->name('conversations.invoice');
    
    // Employee Bookings routes
    Route::get('/bookings', [\App\Http\Controllers\InvoiceController::class, 'employeeBookings'])->name('employee.bookings.index');
    Route::post('/bookings/list', [\App\Http\Controllers\InvoiceController::class, 'getEmployeeBookings'])->name('employee.bookings.list');
    Route::post('/bookings/settle', [\App\Http\Controllers\InvoiceController::class, 'settleBookingAmount'])->name('employee.bookings.settle');
    Route::get('/bookings/{bookingId}/payment-history', [\App\Http\Controllers\InvoiceController::class, 'getPaymentHistory'])->name('employee.bookings.payment-history');
    Route::get('/bookings/{bookingId}/invoice', [\App\Http\Controllers\InvoiceController::class, 'generate'])->name('employee.bookings.invoice');
    Route::post('/bookings/{bookingId}/release', [\App\Http\Controllers\InvoiceController::class, 'releaseTable'])->name('employee.bookings.release');
});

// Admin Company Dashboard Routes
Route::middleware(['web', 'auth:web', 'checkRole:admin'])->prefix('admin')->group(function () {
    Route::get('companies/{id}/dashboard', [\App\Http\Controllers\CompanyController::class, 'dashboard'])->name('admin.companies.dashboard');
    Route::get('companies/{id}/conversations', [\App\Http\Controllers\CompanyController::class, 'getConversations'])->name('admin.companies.conversations');
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
