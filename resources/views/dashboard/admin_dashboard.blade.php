@extends('layouts.app_layout')
@section('style')
    <style>
        .dashboard-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-radius: 10px;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            position: relative;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 5px 0 3px 0;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0;
        }

        .welcome-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            padding: 15px 20px;
        }

        .welcome-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            margin: 0;
        }

        .welcome-time {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.85);
            margin-top: 4px;
        }

        .metric-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            margin: 2px;
        }

        .quick-action-btn {
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 500;
            font-size: 0.85rem;
            border: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .chart-card {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .activity-item {
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            background: #f8f9fa;
        }

        .activity-item:hover {
            background: #e9ecef;
            transform: translateX(3px);
        }

        .text-purple {
            color: #6f42c1 !important;
        }

        .card-body {
            padding: 1rem !important;
        }

        .card-header {
            padding: 0.75rem 1rem !important;
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #e9ecef !important;
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
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-2 mb-3">
                <!-- Total Employees -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-start border-0 border-4 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Total Employees</p>
                                    <h4 class="stat-value text-info">{{ $totalEmployees }}</h4>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge bg-success rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $activeEmployees }} Active</span>
                                        <span class="badge bg-warning rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $inactiveEmployees }} Inactive</span>
                                    </div>
                                </div>
                                <div class="stat-icon bg-info">
                                    <i class="bx bx-user"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Contacts -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-start border-0 border-4 border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Total Contacts</p>
                                    <h4 class="stat-value text-primary">{{ $totalContacts }}</h4>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge bg-warning rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $totalExhibitors }} Exhibitors</span>
                                        <span class="badge bg-success rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $totalVisitors }} Visitors</span>
                                    </div>
                                </div>
                                <div class="stat-icon bg-primary">
                                    <i class="bx bx-group"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Campaigns -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-start border-0 border-4 border-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Total Campaigns</p>
                                    <h4 class="stat-value text-danger">{{ $totalCampaigns }}</h4>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="metric-badge bg-secondary text-white">{{ $draftCampaigns }} Draft</span>
                                        <span class="metric-badge bg-success text-white">{{ $sentCampaigns }} Sent</span>
                                        <span class="metric-badge bg-warning text-white">{{ $scheduledCampaigns }} Scheduled</span>
                                    </div>
                                </div>
                                <div class="stat-icon bg-danger">
                                    <i class="bx bx-megaphone"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Templates -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-start border-0 border-4 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">WhatsApp Templates</p>
                                    <h4 class="stat-value text-success">{{ $totalTemplates }}</h4>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge bg-success rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $approvedTemplates }} Approved</span>
                                        <span class="badge bg-warning rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $pendingTemplates }} Pending</span>
                                    </div>
                                </div>
                                <div class="stat-icon bg-success">
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
                    <div class="card dashboard-card stat-card border-start border-0 border-4 border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Locations</p>
                                    <h4 class="stat-value text-warning">{{ $totalLocations }}</h4>
                                    <small class="text-muted" style="font-size: 0.7rem;">Total locations</small>
                                </div>
                                <div class="stat-icon bg-warning">
                                    <i class="bx bx-map"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Follow-ups -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-start border-0 border-4 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Follow-ups</p>
                                    <h4 class="stat-value text-info">{{ $totalFollowUps }}</h4>
                                    <span class="badge bg-warning rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">{{ $pendingFollowUps }} Pending</span>
                                </div>
                                <div class="stat-icon bg-info">
                                    <i class="bx bx-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings -->
                <div class="col">
                    <div class="card dashboard-card stat-card border-start border-0 border-4 border-purple">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <p class="stat-label">Bookings</p>
                                    <h4 class="stat-value text-purple">{{ $totalBookings }}</h4>
                                    <small class="text-muted" style="font-size: 0.7rem;">Total bookings</small>
                                </div>
                                <div class="stat-icon" style="background: #6f42c1;">
                                    <i class="bx bx-bookmark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col">
                    <div class="card dashboard-card border-start border-0 border-4 border-secondary">
                        <div class="card-body">
                            <p class="stat-label mb-2 fw-bold">Quick Actions</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('employees.create') }}" class="btn btn-info btn-sm quick-action-btn text-white">
                                    <i class="bx bx-user-plus me-1"></i>Add Employee
                                </a>
                                <a href="{{ route('contacts.create', ['type' => 'exhibitor']) }}" class="btn btn-warning btn-sm quick-action-btn text-white">
                                    <i class="bx bx-store-alt me-1"></i>Add Exhibitor
                                </a>
                                <a href="{{ route('contacts.create', ['type' => 'visitor']) }}" class="btn btn-success btn-sm quick-action-btn text-white">
                                    <i class="bx bx-user-circle me-1"></i>Add Visitor
                                </a>
                                <a href="{{ route('campaigns.create') }}" class="btn btn-danger btn-sm quick-action-btn text-white">
                                    <i class="bx bx-plus-circle me-1"></i>Create Campaign
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-lg-8">
                    <div class="card dashboard-card chart-card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-line-chart me-2 text-primary"></i>System Growth Overview
                                </h6>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-info" style="font-size: 0.7rem;"><i class="bx bxs-circle me-1" style="font-size: 0.4rem;"></i>Employees</span>
                                    <span class="badge bg-warning" style="font-size: 0.7rem;"><i class="bx bxs-circle me-1" style="font-size: 0.4rem;"></i>Exhibitors</span>
                                    <span class="badge bg-success" style="font-size: 0.7rem;"><i class="bx bxs-circle me-1" style="font-size: 0.4rem;"></i>Visitors</span>
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
                            <h6 class="mb-0 fw-bold">
                                <i class="bx bx-pie-chart me-2 text-primary"></i>Data Distribution
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
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-user me-2 text-info"></i>Recent Employees
                                </h6>
                                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-info text-white" style="font-size: 0.75rem; padding: 4px 12px;">
                                    View All <i class="bx bx-right-arrow-alt ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="recent-employees-list" style="max-height: 300px; overflow-y: auto;">
                                @forelse($recentEmployees as $employee)
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon bg-info me-2" style="width: 35px; height: 35px; font-size: 16px;">
                                                <i class="bx bx-user"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">{{ $employee->name }}</h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $employee->email }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">
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
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-store-alt me-2 text-warning"></i>Recent Exhibitors
                                </h6>
                                <a href="{{ route('contacts.index', ['type' => 'exhibitor']) }}" class="btn btn-sm btn-warning text-white" style="font-size: 0.75rem; padding: 4px 12px;">
                                    View All <i class="bx bx-right-arrow-alt ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="recent-exhibitors-list" style="max-height: 300px; overflow-y: auto;">
                                @forelse($recentExhibitors as $exhibitor)
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon bg-warning me-2" style="width: 35px; height: 35px; font-size: 16px;">
                                                <i class="bx bx-store-alt"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">{{ $exhibitor->name }}</h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $exhibitor->product_type }} • {{ $exhibitor->location }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-warning rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">Exhibitor</span>
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
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-user-circle me-2 text-success"></i>Recent Visitors
                                </h6>
                                <a href="{{ route('contacts.index', ['type' => 'visitor']) }}" class="btn btn-sm btn-success text-white" style="font-size: 0.75rem; padding: 4px 12px;">
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
                                                <div class="stat-icon bg-success mx-auto mb-2" style="width: 40px; height: 40px; font-size: 18px;">
                                                    <i class="bx bx-user-circle"></i>
                                                </div>
                                                <h6 class="mb-1 fw-bold" style="font-size: 0.85rem;">{{ $visitor->name }}</h6>
                                                <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">{{ $visitor->location }}</small>
                                                <span class="badge bg-success rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">Visitor</span>
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
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-megaphone me-2 text-danger"></i>Recent Campaigns
                                </h6>
                                <a href="{{ route('campaigns.index') }}" class="btn btn-sm btn-danger text-white" style="font-size: 0.75rem; padding: 4px 12px;">
                                    View All <i class="bx bx-right-arrow-alt ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="recent-campaigns-list" style="max-height: 300px; overflow-y: auto;">
                                @forelse($recentCampaigns as $campaign)
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon bg-danger me-2" style="width: 35px; height: 35px; font-size: 16px;">
                                                <i class="bx bx-megaphone"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">{{ $campaign->name }}</h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $campaign->type }} Campaign</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-{{ $campaign->status === 'sent' ? 'success' : ($campaign->status === 'scheduled' ? 'warning' : 'secondary') }} rounded-pill" style="font-size: 0.7rem; padding: 3px 8px;">
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

            // Update Date and Time - Compact format
            function updateDateTime() {
                const now = new Date();
                const dateOptions = { month: 'short', day: 'numeric', year: 'numeric' };
                const timeOptions = { hour: '2-digit', minute: '2-digit' };
                
                const dateStr = now.toLocaleDateString('en-US', dateOptions);
                const timeStr = now.toLocaleTimeString('en-US', timeOptions);
                
                document.getElementById('currentDateTime').textContent = dateStr + ' • ' + timeStr;
            }
            updateDateTime();
            setInterval(updateDateTime, 1000);

            // Chart 1 - Growth Overview
            var ctx = document.getElementById("chart1").getContext('2d');

            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, '#17c5ea');
            gradientStroke1.addColorStop(1, '#6078ea');

            var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, '#ffdf40');
            gradientStroke2.addColorStop(1, '#ff8359');

            var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, '#c92f04');
            gradientStroke3.addColorStop(1, '#f33a06');

            var myChart = new Chart(ctx, {
                type: 'bar',
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

            // Chart 2 - Distribution
            var ctx2 = document.getElementById("chart2").getContext('2d');

            var gradientStroke1 = ctx2.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, '#f7b733');
            gradientStroke1.addColorStop(1, '#fc4a1a');

            var gradientStroke2 = ctx2.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, '#8e54e9');
            gradientStroke2.addColorStop(1, '#4776e6');

            var gradientStroke3 = ctx2.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, '#ff6a00');
            gradientStroke3.addColorStop(1, '#ee0979');

            var myChart2 = new Chart(ctx2, {
                type: 'doughnut',
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
            if (typeof PerfectScrollbar !== 'undefined') {
                new PerfectScrollbar(".recent-employees-list");
                new PerfectScrollbar(".recent-exhibitors-list");
                new PerfectScrollbar(".recent-campaigns-list");
            }
        });
    </script>
@endsection
