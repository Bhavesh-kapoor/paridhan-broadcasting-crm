<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contacts;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalEmployees = User::count();
        $activeEmployees = User::where('status', 'active')->count();
        $inactiveEmployees = User::where('status', 'inactive')->count();

        // Add contact counts
        $totalExhibitors = Contacts::where('type', 'exhibitor')->count();
        $totalVisitors = Contacts::where('type', 'visitor')->count();

        // Recent employees
        $recentEmployees = User::latest()->take(5)->get();

        // Recent contacts
        $recentExhibitors = Contacts::where('type', 'exhibitor')->latest()->take(3)->get();
        $recentVisitors = Contacts::where('type', 'visitor')->latest()->take(3)->get();

        return view('dashboard.admin_dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'totalExhibitors',
            'totalVisitors',
            'recentEmployees',
            'recentExhibitors',
            'recentVisitors'
        ));
    }
}
