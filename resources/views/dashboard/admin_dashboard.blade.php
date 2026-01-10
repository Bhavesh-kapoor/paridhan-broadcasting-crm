@extends('layouts.app_layout')
@section('style')
    <style>
        /* Sidebar-inspired color scheme - smooth and modern */
        :root {
            --sidebar-start: #ec268f; /* Pink from sidebar */
            --sidebar-end: #f06292; /* Light pink from sidebar */
            --primary-color: #ec268f; /* Primary pink */
            --primary-light: #f48fb1;
            --primary-dark: #c2185b;
            --neutral-bg: #f8fafc;
            --neutral-border: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --accent-gray: #94a3b8;
        }

        /* Smooth scrolling */
        * {
            scroll-behavior: smooth;
        }

        .page-content {
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 2px 8px rgba(236, 38, 143, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            background: white;
            overflow: hidden;
            animation: slideInUp 0.5s ease-out backwards;
            border-left: 4px solid transparent;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        }
        
        .dashboard-card:hover {
            border-left-color: var(--sidebar-end);
            box-shadow: 0 4px 16px rgba(236, 38, 143, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-card:nth-child(1) { animation-delay: 0.05s; }
        .dashboard-card:nth-child(2) { animation-delay: 0.1s; }
        .dashboard-card:nth-child(3) { animation-delay: 0.15s; }
        .dashboard-card:nth-child(4) { animation-delay: 0.2s; }
        
        /* Reduce card gaps and ensure even heights */
        .row.g-2 > * {
            padding: 0.5rem;
        }
        
        .stat-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .stat-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }


        .stat-card {
            position: relative;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(236, 38, 143, 0.2);
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 6px 16px rgba(236, 38, 143, 0.3);
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 5px 0 3px 0;
            line-height: 1.2;
            color: var(--text-primary);
            transition: color 0.3s ease;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0;
            transition: color 0.3s ease;
        }

        .welcome-header {
            background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%);
            border-radius: 12px;
            padding: 18px 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInDown 0.5s ease-out;
            box-shadow: 0 4px 16px rgba(236, 38, 143, 0.2);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-header:hover {
            box-shadow: 0 6px 20px rgba(236, 38, 143, 0.3);
            transform: translateY(-2px);
        }

        .welcome-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            margin: 0;
            transition: all 0.3s ease;
        }

        .welcome-time {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 4px;
            transition: opacity 0.3s ease;
        }

        .metric-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            margin: 2px;
            background: var(--neutral-bg);
            color: var(--text-secondary);
            border: 1px solid var(--neutral-border);
        }

        .quick-action-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            font-size: 0.85rem;
            background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(236, 38, 143, 0.2);
        }

        .quick-action-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .quick-action-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .quick-action-btn:hover {
            background: linear-gradient(135deg, var(--sidebar-end) 0%, var(--sidebar-start) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.4);
        }

        .chart-card {
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(236, 38, 143, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
            border-left: 4px solid transparent;
        }

        .chart-card:hover {
            border-left-color: var(--sidebar-end);
            box-shadow: 0 4px 16px rgba(236, 38, 143, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .activity-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid var(--neutral-border);
            border-left: 3px solid transparent;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        .activity-item:hover {
            background: linear-gradient(135deg, #f1f5f9 0%, #f8fafc 100%);
            border-left-color: var(--sidebar-end);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
        }

        .activity-item:active {
            transform: translateX(2px);
        }

        .card-body {
            padding: 1rem !important;
        }

        .card-header {
            padding: 1rem 1.25rem !important;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            border-bottom: 1px solid var(--neutral-border) !important;
            border-radius: 12px 12px 0 0;
        }

        /* Consistent border colors - all use sidebar gradient */
        .border-primary-custom {
            border-left: 4px solid var(--sidebar-end) !important;
        }

        /* Badge colors - soft and consistent */
        .badge-soft {
            background: var(--neutral-bg);
            color: var(--text-secondary);
            border: 1px solid var(--neutral-border);
        }

        .badge-primary-soft {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
            color: var(--sidebar-end);
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }

        .badge-primary-soft:hover {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.15) 0%, rgba(59, 130, 246, 0.15) 100%);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        /* Smooth card borders */
        .dashboard-card .card-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid var(--neutral-border);
            border-radius: 12px 12px 0 0;
            padding: 1rem 1.25rem;
        }
        
        /* Smooth stat cards */
        .stat-card {
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
        }
        
        .stat-card:hover {
            border-left-color: var(--sidebar-end);
            box-shadow: 0 4px 16px rgba(30, 58, 138, 0.12);
            transform: translateY(-2px);
        }

        /* Smooth number animations */
        .stat-value {
            animation: countUp 0.8s ease-out;
        }

        @keyframes countUp {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Smooth list scrolling */
        .recent-employees-list,
        .recent-exhibitors-list,
        .recent-campaigns-list {
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) var(--neutral-bg);
        }

        .recent-employees-list::-webkit-scrollbar,
        .recent-exhibitors-list::-webkit-scrollbar,
        .recent-campaigns-list::-webkit-scrollbar {
            width: 6px;
        }

        .recent-employees-list::-webkit-scrollbar-track,
        .recent-exhibitors-list::-webkit-scrollbar-track,
        .recent-campaigns-list::-webkit-scrollbar-track {
            background: var(--neutral-bg);
            border-radius: 10px;
        }

        .recent-employees-list::-webkit-scrollbar-thumb,
        .recent-exhibitors-list::-webkit-scrollbar-thumb,
        .recent-campaigns-list::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        .recent-employees-list::-webkit-scrollbar-thumb:hover,
        .recent-exhibitors-list::-webkit-scrollbar-thumb:hover,
        .recent-campaigns-list::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Loading state */
        .card-body {
            will-change: transform;
        }

        /* Reduce motion for users who prefer it */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Smooth chart loading */
        canvas {
            transition: opacity 0.5s ease-in-out;
        }
    </style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Compact Welcome Section -->
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="welcome-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="welcome-text mb-1">
                                    <i class="bx bx-hand me-2"></i>Welcome Back! 
                                    <span class="welcome-time d-block mt-1" id="currentDateTime"></span>
                                </h5>
                            </div>
                            <div>
                                <i class="bx bx-line-chart text-white opacity-75" style="font-size: 1.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards Row 1 -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-2 mb-2">
                <!-- Total Employees -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-primary-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Total Employees</p>
                                    <h4 class="stat-value">{{ $totalEmployees }}</h4>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge badge-primary-soft rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $activeEmployees }} Active</span>
                                        <span class="badge badge-soft rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $inactiveEmployees }} Inactive</span>
                                    </div>
                                </div>
                                <div class="stat-icon">
                                    <i class="bx bx-user"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Contacts -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-primary-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Total Contacts</p>
                                    <h4 class="stat-value">{{ $totalContacts }}</h4>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge badge-soft rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $totalExhibitors }} Exhibitors</span>
                                        <span class="badge badge-primary-soft rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $totalVisitors }} Visitors</span>
                                    </div>
                                </div>
                                <div class="stat-icon">
                                    <i class="bx bx-group"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Campaigns -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-primary-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Total Campaigns</p>
                                    <h4 class="stat-value">{{ $totalCampaigns }}</h4>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="metric-badge">{{ $draftCampaigns }} Draft</span>
                                        <span class="metric-badge badge-primary-soft">{{ $sentCampaigns }} Sent</span>
                                        <span class="metric-badge">{{ $scheduledCampaigns }} Scheduled</span>
                                    </div>
                                </div>
                                <div class="stat-icon">
                                    <i class="bx bx-megaphone"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Templates -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-primary-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">WhatsApp Templates</p>
                                    <h4 class="stat-value">{{ $totalTemplates }}</h4>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge badge-primary-soft rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $approvedTemplates }} Approved</span>
                                        <span class="badge badge-soft rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $pendingTemplates }} Pending</span>
                                    </div>
                                </div>
                                <div class="stat-icon">
                                    <i class="bx bx-message-dots"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards Row 2 -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-2 mb-3">
                <!-- Locations -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-primary-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Locations</p>
                                    <h4 class="stat-value">{{ $totalLocations }}</h4>
                                    <small class="text-muted" style="font-size: 0.7rem;">Total locations</small>
                                </div>
                                <div class="stat-icon">
                                    <i class="bx bx-map"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Follow-ups -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-primary-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Follow-ups</p>
                                    <h4 class="stat-value">{{ $totalFollowUps }}</h4>
                                    <span class="badge badge-soft rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $pendingFollowUps }} Pending</span>
                                </div>
                                <div class="stat-icon">
                                    <i class="bx bx-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-primary-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Bookings</p>
                                    <h4 class="stat-value">{{ $totalBookings }}</h4>
                                    <small class="text-muted" style="font-size: 0.7rem;">Total bookings</small>
                                </div>
                                <div class="stat-icon">
                                    <i class="bx bx-bookmark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col">
                    <div class="card dashboard-card border-primary-custom">
                        <div class="card-body">
                            <p class="stat-label mb-2 fw-bold">Quick Actions</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('employees.create') }}" class="btn btn-sm quick-action-btn">
                                    <i class="bx bx-user-plus me-1"></i>Add Employee
                                </a>
                                <a href="{{ route('contacts.create', ['type' => 'exhibitor']) }}" class="btn btn-sm quick-action-btn">
                                    <i class="bx bx-store-alt me-1"></i>Add Exhibitor
                                </a>
                                <a href="{{ route('contacts.create', ['type' => 'visitor']) }}" class="btn btn-sm quick-action-btn">
                                    <i class="bx bx-user-circle me-1"></i>Add Visitor
                                </a>
                                <a href="{{ route('campaigns.create') }}" class="btn btn-sm quick-action-btn">
                                    <i class="bx bx-plus-circle me-1"></i>Create Campaign
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Generation Section -->
            <div class="row g-2 mb-2">
                <div class="col-12">
                    <div class="card dashboard-card border-0 shadow-sm">
                        <div class="card-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bx bx-rupee me-2" style="color: var(--sidebar-end);"></i>Revenue Generation & Analytics
                                </h5>
                                <div class="d-flex gap-2">
                                    <select id="revenueFilter" class="form-select form-select-sm" style="width: auto;">
                                        <option value="all">All Time</option>
                                        <option value="today">Today</option>
                                        <option value="7days">Last 7 Days</option>
                                        <option value="30days" selected>Last 30 Days</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                    <div id="customDateRange" style="display: none;" class="d-flex gap-2">
                                        <input type="date" id="dateFrom" class="form-control form-control-sm" style="width: auto;">
                                        <input type="date" id="dateTo" class="form-control form-control-sm" style="width: auto;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <div class="card stat-card border-0" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-left: 4px solid #22c55e;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <p class="stat-label mb-1">Total Revenue</p>
                                                    <h4 class="stat-value mb-0" id="totalRevenueDisplay">₹{{ number_format($totalRevenue, 2) }}</h4>
                                                </div>
                                                <div class="stat-icon" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                                                    <i class="bx bx-rupee"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card stat-card border-0" style="background: linear-gradient(135deg, #fce7f3 0%, #ffffff 100%); border-left: 4px solid #ec268f;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <p class="stat-label mb-1">Paid Amount</p>
                                                    <h4 class="stat-value mb-0" id="paidRevenueDisplay">₹{{ number_format($paidBookings, 2) }}</h4>
                                                </div>
                                                <div class="stat-icon" style="background: linear-gradient(135deg, #ec268f 0%, #f06292 100%);">
                                                    <i class="bx bx-wallet"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card stat-card border-0" style="background: linear-gradient(135deg, #fef3c7 0%, #ffffff 100%); border-left: 4px solid #f59e0b;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <p class="stat-label mb-1">Pending Revenue</p>
                                                    <h4 class="stat-value mb-0" id="pendingRevenueDisplay">₹{{ number_format($pendingRevenue, 2) }}</h4>
                                                </div>
                                                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                                    <i class="bx bx-time"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card stat-card border-0" style="background: linear-gradient(135deg, #fce7f3 0%, #ffffff 100%); border-left: 4px solid #ec4899;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <p class="stat-label mb-1">Partial Paid</p>
                                                    <h4 class="stat-value mb-0" id="partialRevenueDisplay">₹{{ number_format($partialRevenue, 2) }}</h4>
                                                </div>
                                                <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                                                    <i class="bx bx-wallet-alt"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Top Campaigns by Revenue -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="fw-bold mb-3">
                                        <i class="bx bx-trophy me-2" style="color: var(--sidebar-end);"></i>Top Revenue Generating Campaigns
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-header-gradient">
                                                <tr>
                                                    <th><i class="bx bx-hash"></i> #</th>
                                                    <th><i class="bx bx-megaphone"></i> Campaign</th>
                                                    <th class="text-center"><i class="bx bx-send"></i> Messages</th>
                                                    <th class="text-center"><i class="bx bx-user-plus"></i> Leads</th>
                                                    <th class="text-center"><i class="bx bx-check-circle"></i> Bookings</th>
                                                    <th class="text-end"><i class="bx bx-rupee"></i> Revenue</th>
                                                    <th class="text-center"><i class="bx bx-trending-up"></i> Conversion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($topCampaigns as $index => $campaign)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <strong>{{ $campaign->name }}</strong>
                                                        <br><small class="text-muted">{{ Str::limit($campaign->subject, 40) }}</small>
                                                    </td>
                                                    <td class="text-center">{{ $campaign->messages_sent_count ?? 0 }}</td>
                                                    <td class="text-center">{{ $campaign->leads_count ?? 0 }}</td>
                                                    <td class="text-center">{{ $campaign->bookings_count ?? 0 }}</td>
                                                    <td class="text-end fw-bold text-success">₹{{ number_format($campaign->total_revenue ?? 0, 2) }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info">{{ number_format($campaign->conversion_percentage ?? 0, 1) }}%</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">No revenue data available</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row g-2 mb-2">
                <div class="col-12 col-lg-8">
                    <div class="card dashboard-card chart-card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                                    <i class="bx bx-line-chart me-2" style="color: var(--primary-color);"></i>System Growth Overview
                                </h6>
                                <div class="d-flex gap-1">
                                    <span class="badge badge-soft" style="font-size: 0.7rem;"><i class="bx bxs-circle me-1" style="font-size: 0.4rem;"></i>Employees</span>
                                    <span class="badge badge-primary-soft" style="font-size: 0.7rem;"><i class="bx bxs-circle me-1" style="font-size: 0.4rem;"></i>Exhibitors</span>
                                    <span class="badge badge-soft" style="font-size: 0.7rem;"><i class="bx bxs-circle me-1" style="font-size: 0.4rem;"></i>Visitors</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-1">
                                <canvas id="chart1"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card dashboard-card chart-card">
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                                <i class="bx bx-pie-chart me-2" style="color: var(--primary-color);"></i>Data Distribution
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-2">
                                <canvas id="chart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Section -->
            <div class="row g-2 mb-3">
                <!-- Recent Employees -->
                <div class="col-lg-6">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                                    <i class="bx bx-user me-2" style="color: var(--primary-color);"></i>Recent Employees
                                </h6>
                                <a href="{{ route('employees.index') }}" class="btn btn-sm quick-action-btn" style="font-size: 0.75rem; padding: 4px 12px;">
                                    View All <i class="bx bx-right-arrow-alt ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="recent-employees-list" style="max-height: 300px; overflow-y: auto;">
                                @forelse($recentEmployees as $employee)
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon me-2" style="width: 35px; height: 35px; font-size: 16px;">
                                                <i class="bx bx-user"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">{{ $employee->name }}</h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $employee->email }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge {{ $employee->status === 'active' ? 'badge-primary-soft' : 'badge-soft' }} rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">
                                                    {{ ucfirst($employee->status) }}
                                                </span>
                                                <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">{{ $employee->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="bx bx-user text-muted mb-2" style="font-size: 2.5rem;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.85rem;">No employees found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Exhibitors -->
                <div class="col-lg-6">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                                    <i class="bx bx-store-alt me-2" style="color: var(--primary-color);"></i>Recent Exhibitors
                                </h6>
                                <a href="{{ route('contacts.index', ['type' => 'exhibitor']) }}" class="btn btn-sm quick-action-btn" style="font-size: 0.75rem; padding: 4px 12px;">
                                    View All <i class="bx bx-right-arrow-alt ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="recent-exhibitors-list" style="max-height: 300px; overflow-y: auto;">
                                @forelse($recentExhibitors as $exhibitor)
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon me-2" style="width: 35px; height: 35px; font-size: 16px;">
                                                <i class="bx bx-store-alt"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">{{ $exhibitor->name }}</h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $exhibitor->product_type }} • {{ $exhibitor->location }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge badge-primary-soft rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">Exhibitor</span>
                                                <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">{{ $exhibitor->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="bx bx-store-alt text-muted mb-2" style="font-size: 2.5rem;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.85rem;">No exhibitors found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Visitors & Campaigns -->
            <div class="row g-2">
                <!-- Recent Visitors -->
                <div class="col-lg-8">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                                    <i class="bx bx-user-circle me-2" style="color: var(--primary-color);"></i>Recent Visitors
                                </h6>
                                <a href="{{ route('contacts.index', ['type' => 'visitor']) }}" class="btn btn-sm quick-action-btn" style="font-size: 0.75rem; padding: 4px 12px;">
                                    View All <i class="bx bx-right-arrow-alt ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="row g-2">
                                @forelse($recentVisitors as $visitor)
                                    <div class="col-md-4">
                                        <div class="activity-item h-100">
                                            <div class="text-center">
                                                <div class="stat-icon mx-auto mb-2" style="width: 40px; height: 40px; font-size: 18px;">
                                                    <i class="bx bx-user-circle"></i>
                                                </div>
                                                <h6 class="mb-1 fw-bold" style="font-size: 0.85rem;">{{ $visitor->name }}</h6>
                                                <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">{{ $visitor->location }}</small>
                                                <span class="badge badge-soft rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">Visitor</span>
                                                <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">{{ $visitor->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4">
                                        <i class="bx bx-user-circle text-muted mb-2" style="font-size: 2.5rem;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.85rem;">No visitors found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Campaigns -->
                <div class="col-lg-4">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                                    <i class="bx bx-megaphone me-2" style="color: var(--primary-color);"></i>Recent Campaigns
                                </h6>
                                <a href="{{ route('campaigns.index') }}" class="btn btn-sm quick-action-btn" style="font-size: 0.75rem; padding: 4px 12px;">
                                    View All <i class="bx bx-right-arrow-alt ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="recent-campaigns-list" style="max-height: 300px; overflow-y: auto;">
                                @forelse($recentCampaigns as $campaign)
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon me-2" style="width: 35px; height: 35px; font-size: 16px;">
                                                <i class="bx bx-megaphone"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">{{ $campaign->name }}</h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $campaign->type }} Campaign</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge {{ $campaign->status === 'sent' ? 'badge-primary-soft' : 'badge-soft' }} rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">
                                                    {{ ucfirst($campaign->status) }}
                                                </span>
                                                <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">{{ $campaign->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="bx bx-megaphone text-muted mb-2" style="font-size: 2.5rem;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.85rem;">No campaigns found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('/assets/plugins/chartjs/js/chart.js') }}"></script>
    <script>
        $(function() {
            "use strict";

            // Update Date and Time - Compact format with smooth transition
            function updateDateTime() {
                const now = new Date();
                const dateOptions = { month: 'short', day: 'numeric', year: 'numeric' };
                const timeOptions = { hour: '2-digit', minute: '2-digit' };
                
                const dateStr = now.toLocaleDateString('en-US', dateOptions);
                const timeStr = now.toLocaleTimeString('en-US', timeOptions);
                
                const dateTimeEl = document.getElementById('currentDateTime');
                if (dateTimeEl) {
                    dateTimeEl.style.opacity = '0.5';
                    setTimeout(() => {
                        dateTimeEl.textContent = dateStr + ' • ' + timeStr;
                        dateTimeEl.style.opacity = '1';
                    }, 150);
                }
            }
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            // Revenue filter functionality
            $('#revenueFilter').on('change', function() {
                const filter = $(this).val();
                const customRange = $('#customDateRange');
                
                if (filter === 'custom') {
                    customRange.show();
                } else {
                    customRange.hide();
                    loadRevenueData(filter);
                }
            });
            
            $('#dateFrom, #dateTo').on('change', function() {
                if ($('#dateFrom').val() && $('#dateTo').val()) {
                    loadRevenueData('custom', $('#dateFrom').val(), $('#dateTo').val());
                }
            });
            
            function loadRevenueData(filter, dateFrom = null, dateTo = null) {
                $.ajax({
                    url: '{{ route("admin.dashboard.revenue") }}',
                    type: 'POST',
                    data: {
                        filter: filter,
                        date_from: dateFrom,
                        date_to: dateTo,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#totalRevenueDisplay').text('₹' + parseFloat(response.total_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#paidRevenueDisplay').text('₹' + parseFloat(response.paid_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#pendingRevenueDisplay').text('₹' + parseFloat(response.pending_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#partialRevenueDisplay').text('₹' + parseFloat(response.partial_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }
                    }
                });
            }

            // Chart 1 - Growth Overview (Soft, consistent colors)
            var ctx = document.getElementById("chart1");
            if (!ctx) return;
            ctx = ctx.getContext('2d');

            // Use consistent primary color with variations
            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, '#4a6fa5');
            gradientStroke1.addColorStop(1, '#6b8fc7');

            var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, '#6b8fc7');
            gradientStroke2.addColorStop(1, '#8ba5d4');

            var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, '#3a5a8a');
            gradientStroke3.addColorStop(1, '#4a6fa5');

            var myChart = new Chart(ctx, {
                type: 'bar',
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Employees',
                        data: [{{ $totalEmployees }}, {{ max(0, $totalEmployees - 2) }}, {{ max(0, $totalEmployees - 1) }}, {{ $totalEmployees }}, {{ $totalEmployees }}, {{ $totalEmployees }}, {{ $totalEmployees }}, {{ $totalEmployees }}, {{ $totalEmployees }}, {{ $totalEmployees }}, {{ $totalEmployees }}, {{ $totalEmployees }}],
                        borderColor: gradientStroke1,
                        backgroundColor: gradientStroke1,
                        hoverBackgroundColor: gradientStroke1,
                        borderRadius: 8,
                        borderWidth: 0
                    }, {
                        label: 'Exhibitors',
                        data: [{{ $totalExhibitors }}, {{ max(0, $totalExhibitors - 1) }}, {{ $totalExhibitors }}, {{ $totalExhibitors }}, {{ $totalExhibitors }}, {{ $totalExhibitors }}, {{ $totalExhibitors }}, {{ $totalExhibitors }}, {{ $totalExhibitors }}, {{ $totalExhibitors }}, {{ $totalExhibitors }}, {{ $totalExhibitors }}],
                        borderColor: gradientStroke2,
                        backgroundColor: gradientStroke2,
                        hoverBackgroundColor: gradientStroke2,
                        borderRadius: 8,
                        borderWidth: 0
                    }, {
                        label: 'Visitors',
                        data: [{{ $totalVisitors }}, {{ max(0, $totalVisitors - 2) }}, {{ max(0, $totalVisitors - 1) }}, {{ $totalVisitors }}, {{ $totalVisitors }}, {{ $totalVisitors }}, {{ $totalVisitors }}, {{ $totalVisitors }}, {{ $totalVisitors }}, {{ $totalVisitors }}, {{ $totalVisitors }}, {{ $totalVisitors }}],
                        borderColor: gradientStroke3,
                        backgroundColor: gradientStroke3,
                        hoverBackgroundColor: gradientStroke3,
                        borderRadius: 8,
                        borderWidth: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1,
                            cornerRadius: 6,
                            padding: 10
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // Chart 2 - Distribution (Soft, consistent colors)
            var ctx2 = document.getElementById("chart2");
            if (!ctx2) return;
            ctx2 = ctx2.getContext('2d');

            // Use consistent primary color with variations
            var gradientStroke1 = ctx2.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, '#4a6fa5');
            gradientStroke1.addColorStop(1, '#6b8fc7');

            var gradientStroke2 = ctx2.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, '#6b8fc7');
            gradientStroke2.addColorStop(1, '#8ba5d4');

            var gradientStroke3 = ctx2.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, '#3a5a8a');
            gradientStroke3.addColorStop(1, '#4a6fa5');

            var myChart2 = new Chart(ctx2, {
                type: 'doughnut',
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                data: {
                    labels: ["Employees", "Exhibitors", "Visitors"],
                    datasets: [{
                        backgroundColor: [gradientStroke1, gradientStroke2, gradientStroke3],
                        hoverBackgroundColor: [gradientStroke1, gradientStroke2, gradientStroke3],
                        data: [{{ $totalEmployees }}, {{ $totalExhibitors }}, {{ $totalVisitors }}],
                        borderWidth: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: 11,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1,
                            cornerRadius: 6,
                            padding: 10
                        }
                    }
                }
            });
        });

        // Perfect Scrollbar for lists
        $(document).ready(function() {
            // Initialize smooth scrolling for activity lists
            if (typeof PerfectScrollbar !== 'undefined') {
                const ps1 = new PerfectScrollbar(".recent-employees-list", {
                    wheelSpeed: 0.5,
                    wheelPropagation: false,
                    minScrollbarLength: 20
                });
                const ps2 = new PerfectScrollbar(".recent-exhibitors-list", {
                    wheelSpeed: 0.5,
                    wheelPropagation: false,
                    minScrollbarLength: 20
                });
                const ps3 = new PerfectScrollbar(".recent-campaigns-list", {
                    wheelSpeed: 0.5,
                    wheelPropagation: false,
                    minScrollbarLength: 20
                });
            }

            // Add smooth fade-in for cards with staggered delay
            $('.dashboard-card').each(function(index) {
                $(this).css({
                    'animation-delay': (index * 0.1) + 's'
                });
            });

            // Smooth number animations for stats
            $('.stat-value').each(function() {
                const $this = $(this);
                const originalText = $this.text();
                $this.text('0').animate({
                    opacity: 1
                }, 800, function() {
                    $this.text(originalText);
                });
            });
        });
    </script>
@endsection
