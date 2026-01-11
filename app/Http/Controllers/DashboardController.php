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
        // Check if user is employee or admin
        if (auth()->user()->role === 'employee') {
            return $this->employeeDashboard();
        }
        
        // Admin Dashboard
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

        // Revenue Statistics
        $totalRevenue = Booking::sum('amount_paid') ?? 0;
        $paidBookings = Booking::where('amount_status', 'paid')->sum('amount_paid') ?? 0;
        $pendingRevenue = Booking::where('amount_status', 'pending')->sum('price') ?? 0;
        $partialRevenue = Booking::where('amount_status', 'partial')->sum('amount_paid') ?? 0;
        
        // Campaign Revenue Statistics
        $campaignAnalyticsService = app(\App\Services\CampaignAnalyticsService::class);
        $campaignsRevenue = $campaignAnalyticsService->getAllCampaignsRevenue();
        $topCampaigns = $campaignsRevenue->sortByDesc('total_revenue')->take(5);
        
        // Revenue by date range (default: last 30 days)
        $revenueLast30Days = Booking::where('created_at', '>=', now()->subDays(30))
            ->sum('amount_paid') ?? 0;
        $revenueLast7Days = Booking::where('created_at', '>=', now()->subDays(7))
            ->sum('amount_paid') ?? 0;
        $revenueToday = Booking::whereDate('created_at', today())
            ->sum('amount_paid') ?? 0;

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
            'totalRevenue',
            'paidBookings',
            'pendingRevenue',
            'partialRevenue',
            'revenueLast30Days',
            'revenueLast7Days',
            'revenueToday',
            'topCampaigns',
            'recentEmployees',
            'recentExhibitors',
            'recentVisitors',
            'recentCampaigns'
        ));
    }

    /**
     * Employee Dashboard
     */
    private function employeeDashboard()
    {
        $employeeId = auth()->id();
        
        // Active campaigns (sent or scheduled)
        $activeCampaigns = Campaign::whereIn('status', ['sent', 'scheduled'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Employee's conversations
        $myConversations = \App\Models\Conversation::where('employee_id', $employeeId)
            ->with(['exhibitor', 'visitor', 'campaign', 'location', 'table'])
            ->orderBy('conversation_date', 'desc')
            ->take(10)
            ->get();
        
        // Employee's bookings
        $myBookings = Booking::where('employee_id', $employeeId)
            ->with(['exhibitor', 'visitor', 'campaign', 'location', 'table'])
            ->orderBy('booking_date', 'desc')
            ->take(10)
            ->get();
        
        // Statistics
        $totalConversations = \App\Models\Conversation::where('employee_id', $employeeId)->count();
        $totalBookings = Booking::where('employee_id', $employeeId)->count();
        $totalRevenue = Booking::where('employee_id', $employeeId)->sum('amount_paid') ?? 0;
        
        // Revenue breakdown
        $paidRevenue = Booking::where('employee_id', $employeeId)
            ->where('amount_status', 'paid')
            ->sum('amount_paid') ?? 0;
        $pendingRevenue = Booking::where('employee_id', $employeeId)
            ->where('amount_status', 'pending')
            ->sum('price') ?? 0;
        $partialRevenue = Booking::where('employee_id', $employeeId)
            ->where('amount_status', 'partial')
            ->sum('amount_paid') ?? 0;
        $totalPrice = Booking::where('employee_id', $employeeId)->sum('price') ?? 0;
        
        // Recent revenue (last 30 days)
        $revenueLast30Days = Booking::where('employee_id', $employeeId)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('amount_paid') ?? 0;
        
        // Additional statistics
        $todayConversations = \App\Models\Conversation::where('employee_id', $employeeId)
            ->whereDate('conversation_date', today())
            ->count();
        $todayBookings = Booking::where('employee_id', $employeeId)
            ->whereDate('booking_date', today())
            ->count();
        $todayRevenue = Booking::where('employee_id', $employeeId)
            ->whereDate('booking_date', today())
            ->sum('amount_paid') ?? 0;
        
        // Conversion statistics
        $materialisedConversations = \App\Models\Conversation::where('employee_id', $employeeId)
            ->where('outcome', 'materialised')
            ->count();
        $interestedConversations = \App\Models\Conversation::where('employee_id', $employeeId)
            ->where('outcome', 'interested')
            ->count();
        $busyConversations = \App\Models\Conversation::where('employee_id', $employeeId)
            ->where('outcome', 'busy')
            ->count();
        
        // Active campaigns count
        $totalActiveCampaigns = Campaign::whereIn('status', ['sent', 'scheduled'])->count();
        
        // Conversion rate
        $conversionRate = $totalConversations > 0 
            ? round(($materialisedConversations / $totalConversations) * 100, 1) 
            : 0;
        
        // Revenue by status breakdown
        $thisWeekRevenue = Booking::where('employee_id', $employeeId)
            ->where('booking_date', '>=', now()->startOfWeek())
            ->sum('amount_paid') ?? 0;
        $thisMonthRevenue = Booking::where('employee_id', $employeeId)
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->sum('amount_paid') ?? 0;
        
        return view('dashboard.employee_dashboard', compact(
            'activeCampaigns',
            'myConversations',
            'myBookings',
            'totalConversations',
            'totalBookings',
            'totalRevenue',
            'paidRevenue',
            'pendingRevenue',
            'partialRevenue',
            'totalPrice',
            'revenueLast30Days',
            'todayConversations',
            'todayBookings',
            'todayRevenue',
            'materialisedConversations',
            'interestedConversations',
            'busyConversations',
            'totalActiveCampaigns',
            'conversionRate',
            'thisWeekRevenue',
            'thisMonthRevenue'
        ));
    }

    /**
     * Get revenue data with filters (AJAX)
     */
    public function getRevenueData(Request $request)
    {
        $filter = $request->input('filter', '30days');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        $query = Booking::query();
        
        switch ($filter) {
            case 'today':
                $query->whereDate('booking_date', today());
                break;
            case '7days':
                $query->where('booking_date', '>=', now()->subDays(7)->toDateString());
                break;
            case '30days':
                $query->where('booking_date', '>=', now()->subDays(30)->toDateString());
                break;
            case 'custom':
                if ($dateFrom && $dateTo) {
                    $query->whereBetween('booking_date', [$dateFrom, $dateTo]);
                }
                break;
            default:
                // All time - no filter
                break;
        }
        
        // Clone query for each calculation to avoid query reuse issues
        $baseQuery = clone $query;
        $totalRevenue = (clone $baseQuery)->sum('amount_paid') ?? 0;
        $paidRevenue = (clone $baseQuery)->where('amount_status', 'paid')->sum('amount_paid') ?? 0;
        $pendingRevenue = (clone $baseQuery)->where('amount_status', 'pending')->sum('price') ?? 0;
        $partialRevenue = (clone $baseQuery)->where('amount_status', 'partial')->sum('amount_paid') ?? 0;
        
        return response()->json([
            'status' => true,
            'total_revenue' => $totalRevenue,
            'paid_revenue' => $paidRevenue,
            'pending_revenue' => $pendingRevenue,
            'partial_revenue' => $partialRevenue,
        ]);
    }
}
