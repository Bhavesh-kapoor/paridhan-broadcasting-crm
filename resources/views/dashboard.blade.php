@extends('layout.master')
@section('title', 'Dashboard')
@section('css')

    <style>
        /* Enhanced Card Styles */
        .card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15) !important;
        }

        /* Stats Cards */
        .stats-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }

        .stats-card:hover {
            background: linear-gradient(145deg, #ffffff 0%, #e9ecef 100%);
        }

        /* Chart Cards */
        .chart-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }

        .chart-card:hover {
            background: linear-gradient(145deg, #ffffff 0%, #e9ecef 100%);
        }

        /* Activity Cards */
        .activity-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }

        .activity-card:hover {
            background: linear-gradient(145deg, #ffffff 0%, #e9ecef 100%);
        }

        /* Activity Items */
        .activity-item {
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: translateX(5px);
        }

        /* Metric Boxes */
        .metric-box {
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .metric-box:hover {
            border-color: rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        /* Button Styles */
        .btn {
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        /* Progress Bars */
        .progress {
            border-radius: 12px;
            background-color: #f8f9fa;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Badge Styles */
        .badge {
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Typography */
        .display-6 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .fw-medium {
            font-weight: 500;
        }

        /* Rounded Corners */
        .rounded-4 {
            border-radius: 1rem !important;
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            margin: auto;
            padding: 1rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .card-body {
                padding: 1.5rem !important;
            }

            .display-6 {
                font-size: 2rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card border-0 shadow-lg"
                    style="background:white; border-radius: 20px; border-left: 6px solid #6c757d;">
                    <div class="card-body p-5">
                        <div class="row align-items-center" style="margin-left:20px;">
                            <div class="col-lg-8">
                                <h3 class="text-dark mb-3 fw-bold display-8">
                                    <i class="ph ph-wave-hand me-3" style="font-size: 3rem; color: #495057;"></i>Welcome
                                    Back!
                                </h3>
                                <p class="text-muted mb-0 opacity-75">Here's what's happening with your CRM system today.
                                </p>
                            </div>
                            <div class="col-lg-4 text-end">
                                <div class="bg-white bg-opacity-75 rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg"
                                    style="width: 50px; height: 50px;">
                                    <i class="ph ph-chart-line" style="font-size: 2.5rem; color: #6c757d;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards with Enhanced Rectangular Design and Mini Graphs -->
        <div class="row g-4 mb-5">
            <!-- Employees Stats -->
            <div class="col-md-3">
                <div class="card border-0 shadow-lg h-100 stats-card"
                    style="border-radius: 16px; border-left: 6px solid #6c757d;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4" style="padding: 10px;">
                            <div class="bg-secondary bg-opacity-10 rounded-4 p-3" style="width: 70px; height: 70px;">
                                <i
                                    class="ph ph-users text-secondary fs-2 d-flex align-items-center justify-content-center h-100"></i>
                            </div>
                            <div class="text-end">
                                <h2 class="text-dark mb-0 fw-bold display-6">{{ $totalEmployees }}</h2>
                                <small class="text-muted fw-medium">Total</small>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-secondary"
                                style="width: {{ $totalEmployees > 0 ? ($activeEmployees / $totalEmployees) * 100 : 0 }}%">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-success rounded-pill"
                                style="padding: 20px important!;">{{ $activeEmployees }} Active</span>
                            <span class="badge bg-warning rounded-pill"
                                style="padding: 20px important!;">{{ $inactiveEmployees }} Inactive</span>
                        </div>
                        <!-- Mini Graph for Employees -->
                        <div class="mt-4" style="height: 80px; background: #f8f9fa; border-radius: 8px; padding: 10px;">
                            <canvas id="employeeMiniChart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exhibitors Stats -->
            <div class="col-md-3">
                <div class="card border-0 shadow-lg h-100 stats-card"
                    style="border-radius: 16px; border-left: 6px solid #fd7e14;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4" style="padding: 10px;">
                            <div class="bg-warning bg-opacity-10 rounded-4 p-3" style="width: 70px; height: 70px;">
                                <i
                                    class="ph ph-storefront text-warning fs-2 d-flex align-items-center justify-content-center h-100"></i>
                            </div>
                            <div class="text-end">
                                <h2 class="text-dark mb-0 fw-bold display-6">{{ $totalExhibitors }}</h2>
                                <small class="text-muted fw-medium">Total</small>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-warning"
                                style="width: {{ $totalExhibitors > 0 ? min(($totalExhibitors / 10) * 100, 100) : 0 }}%">
                            </div>
                        </div>
                        <div class="text-center">
                            <span class="badge bg-warning rounded-pill" style="padding: 20px important!;">Business
                                Partners</span>
                        </div>
                        <!-- Mini Graph for Exhibitors -->
                        <div class="mt-4" style="height: 80px; background: #f8f9fa; border-radius: 8px; padding: 10px;">
                            <canvas id="exhibitorMiniChart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visitors Stats -->
            <div class="col-md-3">
                <div class="card border-0 shadow-lg h-100 stats-card"
                    style="border-radius: 16px; border-left: 6px solid #20c997;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4" style="padding: 10px;">
                            <div class="bg-success bg-opacity-10 rounded-4 p-3" style="width: 70px; height: 70px;">
                                <i
                                    class="ph ph-user-circle text-success fs-2 d-flex align-items-center justify-content-center h-100"></i>
                            </div>
                            <div class="text-end">
                                <h2 class="text-dark mb-0 fw-bold display-6">{{ $totalVisitors }}</h2>
                                <small class="text-muted fw-medium">Total</small>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success"
                                style="width: {{ $totalVisitors > 0 ? min(($totalVisitors / 20) * 100, 100) : 0 }}%"></div>
                        </div>
                        <div class="text-center">
                            <span class="badge bg-success rounded-pill" style="padding: 20px important!;">Event
                                Attendees</span>
                        </div>
                        <!-- Mini Graph for Visitors -->
                        <div class="mt-4" style="height: 80px; background: #f8f9fa; border-radius: 8px; padding: 10px;">
                            <canvas id="visitorMiniChart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-3">
                <div class="card border-0 shadow-lg h-100 stats-card"
                    style="border-radius: 16px; border-left: 6px solid #6f42c1;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4" style="padding: 10px;">
                            <div class="bg-purple bg-opacity-10 rounded-4 p-3" style="width: 70px; height: 70px;">
                                <i
                                    class="ph ph-plus text-purple fs-2 d-flex align-items-center justify-content-center h-100"></i>
                            </div>
                            <div class="text-end">
                                <h6 class="text-dark mb-0 fw-bold fs-5">Quick Actions</h6>
                                <small class="text-muted fw-medium">Manage</small>
                            </div>
                        </div>
                        <div class="d-grid gap-3" style="padding: 10px;">
                            <a href="{{ route('employees.create') }}" class="btn btn-secondary btn-lg fw-medium"
                                style="border-radius: 8px; padding: 12px 20px; font-size: 14px; text-decoration: none; display: block; text-align: center;">
                                <i class="ph ph-user-plus me-2"></i>Add Employee
                            </a>
                            <a href="{{ route('contacts.create', ['type' => 'exhibitor']) }}"
                                class="btn btn-warning btn-lg fw-medium"
                                style="border-radius: 8px; padding: 12px 20px; font-size: 14px; text-decoration: none; display: block; text-align: center;">
                                <i class="ph ph-storefront me-2"></i>Add Exhibitor
                            </a>
                            <a href="{{ route('contacts.create', ['type' => 'visitor']) }}"
                                class="btn btn-success btn-lg fw-medium"
                                style="border-radius: 8px; padding: 12px 20px; font-size: 14px; text-decoration: none; display: block; text-align: center;">
                                <i class="ph ph-user-circle me-2"></i>Add Visitor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics Section -->
        <div class="row g-4 mb-5">
            <!-- Growth Chart -->
            <div class="col-lg-8" style="margin-top:80px;">
                <div class="card border-0 shadow-lg chart-card"
                    style="border-radius: 20px; border-left: 6px solid #6c757d;">
                    <div class="card-header bg-transparent border-0 py-4 px-4" style="padding: 20px !important;">
                        <h4 class="card-title mb-0 text-dark fw-bold">
                            <i class="ph ph-chart-line me-3 text-secondary"></i>System Growth Overview
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="bg-light rounded-4 p-4 text-center metric-box">
                                    <h6 class="text-muted mb-2 fw-medium">Total Records</h6>
                                    <h3 class="text-secondary mb-0 fw-bold">
                                        {{ $totalEmployees + $totalExhibitors + $totalVisitors }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light rounded-4 p-4 text-center metric-box">
                                    <h6 class="text-muted mb-2 fw-medium">Active Users</h6>
                                    <h3 class="text-success mb-0 fw-bold">{{ $activeEmployees }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light rounded-4 p-4 text-center metric-box">
                                    <h6 class="text-muted mb-2 fw-medium">Business Partners</h6>
                                    <h3 class="text-warning mb-0 fw-bold">{{ $totalExhibitors }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light rounded-4 p-4 text-center metric-box">
                                    <h6 class="text-muted mb-2 fw-medium">Event Attendees</h6>
                                    <h3 class="text-info mb-0 fw-bold">{{ $totalVisitors }}</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Bar Chart -->
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="growthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-lg-4" style="margin-top:80px;">
                <div class="card border-0 shadow-lg chart-card"
                    style="border-radius: 20px; border-left: 6px solid #fd7e14;">
                    <div class="card-header bg-transparent border-0 " style="padding: 20px !important;">
                        <h4 class="card-title mb-0 text-dark fw-bold">
                            <i class="ph ph-chart-pie me-3 text-warning"></i>Data Distribution
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="row g-4 mb-5">
            <!-- Recent Employees -->
            <div class="col-lg-6" style="margin-top:80px;">
                <div class="card border-0 shadow-lg h-100 activity-card"
                    style="border-radius: 20px; border-left: 6px solid #6c757d;">
                    <div class="card-header bg-transparent border-0 "
                        style="padding: 20px !important; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0 text-dark fw-bold">
                                <i class="ph ph-users me-3 text-secondary"></i>Recent Employees
                            </h4>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary fw-medium"
                                style="border-radius: 8px; padding: 10px 20px; font-size: 14px; text-decoration: none; background-color: #6c757d; color: white; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body " style="padding: 10px !important";>
                        @forelse($recentEmployees as $employee)
                            <div class="d-flex align-items-center  border-bottom border-light activity-item"
                                style="margin-bottom: 10px; background-color: #f4f2f2ff; border-radius: 8px;padding: 10px !important;">
                                <div class="bg-secondary bg-opacity-10 rounded-4 p-3 me-4"
                                    style="width: 30px; height: 30px;">
                                    <i
                                        class="ph ph-user text-secondary fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $employee->name }}</h6>
                                    <small class="text-muted">{{ $employee->email }}</small>
                                </div>
                                <div class="text-end">
                                    <span style="padding: 20px important!;"
                                        class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill ">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                    <small
                                        class="d-block text-muted mt-2 fw-medium">{{ $employee->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="ph ph-users text-muted mb-3" style="font-size: 4rem;"></i>
                                <p class="text-muted fw-medium">No employees found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Exhibitors -->
            <div class="col-lg-6" style="margin-top:80px;">
                <div class="card border-0 shadow-lg h-100 activity-card"
                    style="border-radius: 20px; border-left: 6px solid #fd7e14;">
                    <div class="card-header bg-transparent border-0 py-4 px-4"
                        style="padding: 20px !important;  background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0 text-dark fw-bold">
                                <i class="ph ph-storefront me-3 text-warning"></i>Recent Exhibitors
                            </h4>
                            <a href="{{ route('contacts.index', ['type' => 'exhibitor']) }}"
                                class="btn btn-warning fw-medium"
                                style="border-radius: 8px; padding: 10px 20px; font-size: 14px; text-decoration: none; background-color: #fd7e14; color: white; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0" style="padding: 20px;">
                        @forelse($recentExhibitors as $exhibitor)
                            <div class="d-flex align-items-center p-4 border-bottom border-light activity-item"
                                style="margin-bottom: 15px; background-color: #f8f9fa; border-radius: 8px;">
                                <div class="bg-warning bg-opacity-10 rounded-4 p-3 me-4"
                                    style="width: 50px; height: 50px;">
                                    <i
                                        class="ph ph-storefront text-warning fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $exhibitor->name }}</h6>
                                    <small class="text-muted">{{ $exhibitor->product_type }} •
                                        {{ $exhibitor->location }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning rounded-pill "
                                        style="padding: 10px !important;">Exhibitor</span>
                                    <small
                                        class="d-block text-muted mt-2 fw-medium">{{ $exhibitor->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="ph ph-storefront text-muted mb-3" style="font-size: 4rem;"></i>
                                <p class="text-muted fw-medium">No exhibitors found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Visitors Section -->
        <div class="row g-4" style="margin-top:40px;">
            <div class="col-lg-12">
                <div class="card border-0 shadow-lg activity-card"
                    style="border-radius: 20px; border-left: 6px solid #20c997;">
                    <div class="card-header bg-transparent border-0 py-4 px-4"
                        style="padding: 20px !important; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0 text-dark fw-bold">
                                <i class="ph ph-user-circle me-3 text-success"></i>Recent Visitors
                            </h4>
                            <a href="{{ route('contacts.index', ['type' => 'visitor']) }}"
                                class="btn btn-success fw-medium"
                                style="border-radius: 8px; padding: 10px 20px; font-size: 14px; text-decoration: none; background-color: #20c997; color: white; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0" style="padding: 20px;">
                        <div class="row g-0">
                            @forelse($recentVisitors as $visitor)
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center p-4 border-bottom border-end border-light activity-item"
                                        style="margin-bottom: 15px; background-color: #f8f9fa; border-radius: 8px;">
                                        <div class="bg-success bg-opacity-10 rounded-4 p-3 me-4"
                                            style="width: 50px; height: 50px;">
                                            <i
                                                class="ph ph-user-circle text-success fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $visitor->name }}</h6>
                                            <small class="text-muted">{{ $visitor->location }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success rounded-pill "
                                                style="padding: 10px !important;">Visitor</span>
                                            <small
                                                class="d-block text-muted mt-2 fw-medium">{{ $visitor->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <i class="ph ph-user-circle text-muted mb-3" style="font-size: 4rem;"></i>
                                    <p class="text-muted fw-medium">No visitors found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Mini Charts for Stats Cards
            // Employee Mini Chart
            const employeeCtx = document.getElementById('employeeMiniChart').getContext('2d');
            const employeeMiniChart = new Chart(employeeCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Employees',
                        data: [{{ max(0, $totalEmployees - 5) }}, {{ max(0, $totalEmployees - 3) }},
                            {{ max(0, $totalEmployees - 2) }}, {{ max(0, $totalEmployees - 1) }},
                            {{ max(0, $totalEmployees - 1) }}, {{ $totalEmployees }}
                        ],
                        borderColor: '#6c757d',
                        backgroundColor: 'rgba(108, 117, 125, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    },
                    elements: {
                        point: {
                            radius: 0
                        }
                    }
                }
            });

            // Exhibitor Mini Chart
            const exhibitorCtx = document.getElementById('exhibitorMiniChart').getContext('2d');
            const exhibitorMiniChart = new Chart(exhibitorCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Exhibitors',
                        data: [{{ max(0, $totalExhibitors - 3) }}, {{ max(0, $totalExhibitors - 2) }},
                            {{ max(0, $totalExhibitors - 1) }}, {{ max(0, $totalExhibitors - 1) }},
                            {{ max(0, $totalExhibitors - 1) }}, {{ $totalExhibitors }}
                        ],
                        borderColor: '#fd7e14',
                        backgroundColor: 'rgba(253, 126, 20, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    },
                    elements: {
                        point: {
                            radius: 0
                        }
                    }
                }
            });

            // Visitor Mini Chart
            const visitorCtx = document.getElementById('visitorMiniChart').getContext('2d');
            const visitorMiniChart = new Chart(visitorCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Visitors',
                        data: [{{ max(0, $totalVisitors - 8) }}, {{ max(0, $totalVisitors - 5) }},
                            {{ max(0, $totalVisitors - 3) }}, {{ max(0, $totalVisitors - 2) }},
                            {{ max(0, $totalVisitors - 1) }}, {{ $totalVisitors }}
                        ],
                        borderColor: '#20c997',
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    },
                    elements: {
                        point: {
                            radius: 0
                        }
                    }
                }
            });

            // Enhanced Growth Chart (Bar Chart)
            const growthCtx = document.getElementById('growthChart').getContext('2d');
            const growthChart = new Chart(growthCtx, {
                type: 'bar',
                data: {
                    labels: ['Employees', 'Exhibitors', 'Visitors'],
                    datasets: [{
                        label: 'Total Count',
                        data: [{{ $totalEmployees }}, {{ $totalExhibitors }}, {{ $totalVisitors }}],
                        backgroundColor: [
                            'rgba(108, 117, 125, 0.9)',
                            'rgba(253, 126, 20, 0.9)',
                            'rgba(32, 201, 151, 0.9)'
                        ],
                        borderColor: [
                            'rgba(108, 117, 125, 1)',
                            'rgba(253, 126, 20, 1)',
                            'rgba(32, 201, 151, 1)'
                        ],
                        borderWidth: 3,
                        borderRadius: 12,
                        borderSkipped: false,
                        hoverBackgroundColor: [
                            'rgba(108, 117, 125, 1)',
                            'rgba(253, 126, 20, 1)',
                            'rgba(32, 201, 151, 1)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.08)',
                                lineWidth: 1
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                color: '#6c757d'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                color: '#6c757d'
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });

            // Enhanced Distribution Chart (Doughnut Chart)
            const distributionCtx = document.getElementById('distributionChart').getContext('2d');
            const distributionChart = new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Employees', 'Exhibitors', 'Visitors'],
                    datasets: [{
                        data: [{{ $totalEmployees }}, {{ $totalExhibitors }}, {{ $totalVisitors }}],
                        backgroundColor: [
                            'rgba(108, 117, 125, 0.9)',
                            'rgba(253, 126, 20, 0.9)',
                            'rgba(32, 201, 151, 0.9)'
                        ],
                        borderColor: [
                            'rgba(108, 117, 125, 1)',
                            'rgba(253, 126, 20, 1)',
                            'rgba(32, 201, 151, 1)'
                        ],
                        borderWidth: 3,
                        hoverOffset: 8,
                        cutout: '65%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 25,
                                usePointStyle: true,
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                color: '#6c757d'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        </script>
    @endpush
@endsection
