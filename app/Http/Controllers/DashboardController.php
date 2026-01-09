<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contacts;
use App\Models\Campaign;
use App\Models\WhatsAppTemplate;
use App\Models\LocationMngt;
use App\Models\FollowUp;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Employee Statistics
        $totalEmployees = User::count();
        $activeEmployees = User::where('status', 'active')->count();
        $inactiveEmployees = User::where('status', 'inactive')->count();

        // Contact Statistics
        $totalExhibitors = Contacts::where('type', 'exhibitor')->count();
        $totalVisitors = Contacts::where('type', 'visitor')->count();
        $totalContacts = Contacts::count();

        // Campaign Statistics
        $totalCampaigns = Campaign::count();
        $draftCampaigns = Campaign::where('status', 'draft')->count();
        $sentCampaigns = Campaign::where('status', 'sent')->count();
        $scheduledCampaigns = Campaign::where('status', 'scheduled')->count();

        // Template Statistics
        $totalTemplates = WhatsAppTemplate::count();
        $approvedTemplates = WhatsAppTemplate::where('status', 'APPROVED')->count();
        $pendingTemplates = WhatsAppTemplate::where('status', 'PENDING')->count();

        // Location Statistics
        $totalLocations = LocationMngt::count();

        // Follow-up Statistics
        $totalFollowUps = FollowUp::count();
        $pendingFollowUps = FollowUp::where('status', 'pending')->count();

        // Booking Statistics
        $totalBookings = Booking::count();

        // Recent Data
        $recentEmployees = User::latest()->take(5)->get();
        $recentExhibitors = Contacts::where('type', 'exhibitor')->latest()->take(3)->get();
        $recentVisitors = Contacts::where('type', 'visitor')->latest()->take(3)->get();
        $recentCampaigns = Campaign::latest()->take(5)->get();

        return view('dashboard.admin_dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'totalExhibitors',
            'totalVisitors',
            'totalContacts',
            'totalCampaigns',
            'draftCampaigns',
            'sentCampaigns',
            'scheduledCampaigns',
            'totalTemplates',
            'approvedTemplates',
            'pendingTemplates',
            'totalLocations',
            'totalFollowUps',
            'pendingFollowUps',
            'totalBookings',
            'recentEmployees',
            'recentExhibitors',
            'recentVisitors',
            'recentCampaigns'
        ));
    }
}
