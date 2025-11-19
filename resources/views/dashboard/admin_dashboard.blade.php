@extends('layouts.app_layout')
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-primary">
                        <div class="card-body">
                            <div class="row align-items-center ps-3">
                                <div class="col-lg-8">
                                    <h3 class="text-dark mb-3 fw-bold">
                                        <i class="lni lni-hand me-3 fs-1 text-secondary"></i>
                                        Welcome Back!
                                    </h3>
                                    <p class="text-muted mb-0 opacity-75">
                                        Here's what's happening with your CRM system today.
                                    </p>
                                </div>
                                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                                    <div
                                        class="bg-white bg-opacity-75 rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg p-3">
                                        <i class="bx bx-line-chart text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Employee</p>
                                    <h4 class="my-1 text-info">{{ $totalEmployees }}</h4>
                                    <p class="mb-0 font-13">+{{ $totalEmployees - 1 }} from last week</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i
                                        class='lni lni-users'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Business Partners</p>
                                    <h4 class="my-1 text-danger">{{ $totalExhibitors }}</h4>
                                    <p class="mb-0 font-13">+{{ $totalExhibitors - 1 }} from last week</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto">
                                    <i class='bx bx-store-alt'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Visitors</p>
                                    <h4 class="my-1 text-success">{{ $totalVisitors }}</h4>
                                    <p class="mb-0 font-13">-{{ $totalVisitors - 1 }} from last week</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                                    <i class='bx bx-user'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-warning">
                        <div class="card-body">
                            <div class="">
                                <div>
                                    <p class="mb-0 text-secondary">Quick Actions</p>
                                </div>
                                <div class="d-flex flex-column">
                                    <a class="btn btn-sm btn-secondary d-flex align-items-center w-75"
                                        href="{{ route('employees.index') }}"><i
                                            class="fadeIn animated bx bx-user-plus"></i>&nbsp;Add Employee</a>
                                    <a class="btn btn-sm btn-warning d-flex align-items-center w-75 my-2"
                                        href="{{ route('contacts.index', ['type' => 'exhibitor']) }}"><i
                                            class="fadeIn animated bx  bx-store-alt"></i>&nbsp;Add Exhibitor</a>
                                    <a class="btn btn-sm btn-success d-flex align-items-center w-75"
                                        href="{{ route('contacts.index', ['type' => 'visitor']) }}"><i
                                            class="fadeIn animated bx bx-user-circle"></i>&nbsp;Add Visitor</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--end row-->

            <div class="row">
                <div class="col-12 col-lg-8 d-flex">
                    <div class="card radius-10 w-100">
                        <div class="card-header">
                            <div class="d-flex align-items-center py-2">
                                <div>
                                    <h6 class="mb-0">System Growth Overview</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center ms-auto font-13 gap-2 mb-3">
                                <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1"
                                        style="color: #14abef"></i>Employe</span>
                                <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1"
                                        style="color: #ffc107"></i>Exhibitor</span>
                                <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1"
                                        style="color: #f33a06"></i>Visitor</span>
                            </div>
                            <div class="chart-container-1">
                                <canvas id="chart1"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex">
                    <div class="card radius-10 w-100">
                        <div class="card-header">
                            <div class="d-flex align-items-center py-2">
                                <div>
                                    <h6 class="mb-0">Total Count</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-2">
                                <canvas id="chart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--end row-->

            <!-- Recent Activities Section -->
            <div class="row g-4 mb-3">
                <!-- Recent Employees -->
                <div class="col-lg-6">
                    <div class="card border-0 h-100 activity-card"
                        style="border-radius: 20px; border-left: 6px solid #6c757d;">
                        <div class="card-header bg-transparent border-0 "
                            style="padding: 20px !important; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 text-dark fw-bold">
                                    <i class="lni lni-users me-3 text-secondary"></i>Recent Employees
                                </h4>
                                <a href="{{ route('employees.index') }}" class="btn btn-secondary fw-medium"
                                    style="border-radius: 8px; padding: 10px 20px; font-size: 14px; text-decoration: none; background-color: #6c757d; color: white; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body" style="padding: 10px !important;">
                            <div class="recent-employees-list">
                                @forelse($recentEmployees as $employee)
                                    <div class="d-flex align-items-center border-bottom border-light activity-item"
                                        style="margin-bottom: 10px; background-color: #f4f2f2ff; border-radius: 8px;padding: 10px !important;">
                                        <div class="bg-secondary bg-opacity-10 rounded-4 p-3 me-4"
                                            style="width: 30px; height: 30px;">
                                            <i
                                                class="bx bx-user text-secondary fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $employee->name }}</h6>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill ">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                            <small
                                                class="d-block text-muted mt-2 fw-medium">{{ $employee->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="lni lni-users text-muted mb-3" style="font-size: 4rem;"></i>
                                        <p class="text-muted fw-medium">No employees found</p>
                                    </div>
                                @endforelse
                                @forelse($recentEmployees as $employee)
                                    <div class="d-flex align-items-center border-bottom border-light activity-item"
                                        style="margin-bottom: 10px; background-color: #f4f2f2ff; border-radius: 8px;padding: 10px !important;">
                                        <div class="bg-secondary bg-opacity-10 rounded-4 p-3 me-4"
                                            style="width: 30px; height: 30px;">
                                            <i
                                                class="bx bx-user text-secondary fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $employee->name }}</h6>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill ">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                            <small
                                                class="d-block text-muted mt-2 fw-medium">{{ $employee->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="lni lni-users text-muted mb-3" style="font-size: 4rem;"></i>
                                        <p class="text-muted fw-medium">No employees found</p>
                                    </div>
                                @endforelse
                                @forelse($recentEmployees as $employee)
                                    <div class="d-flex align-items-center border-bottom border-light activity-item"
                                        style="margin-bottom: 10px; background-color: #f4f2f2ff; border-radius: 8px;padding: 10px !important;">
                                        <div class="bg-secondary bg-opacity-10 rounded-4 p-3 me-4"
                                            style="width: 30px; height: 30px;">
                                            <i
                                                class="bx bx-user text-secondary fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $employee->name }}</h6>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill ">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                            <small
                                                class="d-block text-muted mt-2 fw-medium">{{ $employee->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="lni lni-users text-muted mb-3" style="font-size: 4rem;"></i>
                                        <p class="text-muted fw-medium">No employees found</p>
                                    </div>
                                @endforelse
                                @forelse($recentEmployees as $employee)
                                    <div class="d-flex align-items-center border-bottom border-light activity-item"
                                        style="margin-bottom: 10px; background-color: #f4f2f2ff; border-radius: 8px;padding: 10px !important;">
                                        <div class="bg-secondary bg-opacity-10 rounded-4 p-3 me-4"
                                            style="width: 30px; height: 30px;">
                                            <i
                                                class="bx bx-user text-secondary fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $employee->name }}</h6>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill ">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                            <small
                                                class="d-block text-muted mt-2 fw-medium">{{ $employee->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="lni lni-users text-muted mb-3" style="font-size: 4rem;"></i>
                                        <p class="text-muted fw-medium">No employees found</p>
                                    </div>
                                @endforelse
                                @forelse($recentEmployees as $employee)
                                    <div class="d-flex align-items-center border-bottom border-light activity-item"
                                        style="margin-bottom: 10px; background-color: #f4f2f2ff; border-radius: 8px;padding: 10px !important;">
                                        <div class="bg-secondary bg-opacity-10 rounded-4 p-3 me-4"
                                            style="width: 30px; height: 30px;">
                                            <i
                                                class="bx bx-user text-secondary fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $employee->name }}</h6>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill ">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                            <small
                                                class="d-block text-muted mt-2 fw-medium">{{ $employee->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="lni lni-users text-muted mb-3" style="font-size: 4rem;"></i>
                                        <p class="text-muted fw-medium">No employees found</p>
                                    </div>
                                @endforelse
                                @forelse($recentEmployees as $employee)
                                    <div class="d-flex align-items-center border-bottom border-light activity-item"
                                        style="margin-bottom: 10px; background-color: #f4f2f2ff; border-radius: 8px;padding: 10px !important;">
                                        <div class="bg-secondary bg-opacity-10 rounded-4 p-3 me-4"
                                            style="width: 30px; height: 30px;">
                                            <i
                                                class="bx bx-user text-secondary fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $employee->name }}</h6>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill ">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                            <small
                                                class="d-block text-muted mt-2 fw-medium">{{ $employee->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="lni lni-users text-muted mb-3" style="font-size: 4rem;"></i>
                                        <p class="text-muted fw-medium">No employees found</p>
                                    </div>
                                @endforelse
                                @forelse($recentEmployees as $employee)
                                    <div class="d-flex align-items-center border-bottom border-light activity-item"
                                        style="margin-bottom: 10px; background-color: #f4f2f2ff; border-radius: 8px;padding: 10px !important;">
                                        <div class="bg-secondary bg-opacity-10 rounded-4 p-3 me-4"
                                            style="width: 30px; height: 30px;">
                                            <i
                                                class="bx bx-user text-secondary fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $employee->name }}</h6>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill ">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                            <small
                                                class="d-block text-muted mt-2 fw-medium">{{ $employee->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="lni lni-users text-muted mb-3" style="font-size: 4rem;"></i>
                                        <p class="text-muted fw-medium">No employees found</p>
                                    </div>
                                @endforelse
                                @forelse($recentEmployees as $employee)
                                    <div class="d-flex align-items-center border-bottom border-light activity-item"
                                        style="margin-bottom: 10px; background-color: #f4f2f2ff; border-radius: 8px;padding: 10px !important;">
                                        <div class="bg-secondary bg-opacity-10 rounded-4 p-3 me-4"
                                            style="width: 30px; height: 30px;">
                                            <i
                                                class="bx bx-user text-secondary fs-5 d-flex align-items-center justify-content-center h-100"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $employee->name }}</h6>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill ">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                            <small
                                                class="d-block text-muted mt-2 fw-medium">{{ $employee->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="lni lni-users text-muted mb-3" style="font-size: 4rem;"></i>
                                        <p class="text-muted fw-medium">No employees found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Exhibitors -->
                <div class="col-lg-6">
                    <div class="card border-0 h-100 activity-card"
                        style="border-radius: 20px; border-left: 6px solid #fd7e14;">
                        <div class="card-header bg-transparent border-0 py-4 px-4"
                            style="padding: 20px !important; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 text-dark fw-bold">
                                    <i class="bx bx-store-alt me-3 text-warning"></i>Recent Exhibitors
                                </h4>
                                <a href="{{ route('contacts.index', ['type' => 'exhibitor']) }}"
                                    class="btn btn-warning fw-medium"
                                    style="border-radius: 8px; padding: 10px 20px; font-size: 14px; text-decoration: none; background-color: #fd7e14; color: white; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0" style="padding: 10px !important;">
                            <div class="recent-exhibitors-list">
                                @forelse($recentExhibitors as $exhibitor)
                                    <div class="d-flex align-items-center p-2 border-bottom border-light activity-item"
                                        style="margin-bottom: 10px; background-color: #f8f9fa; border-radius: 8px;">
                                        <div class="bg-warning bg-opacity-10 rounded-4"
                                            style="width: 50px; height: 50px;">
                                            <i
                                                class="bx bx-store-alt text-warning fs-5 d-flex align-items-center justify-content-center h-100"></i>
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
            </div>

            <!-- Recent Visitors Section -->
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="card border-0  activity-card"
                        style="border-radius: 20px; border-left: 6px solid #20c997;">
                        <div class="card-header bg-transparent border-0 py-4 px-4"
                            style="padding: 20px !important; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 text-dark fw-bold">
                                    <i class="fadeIn animated bx bx-user-circle me-3 text-success"></i>Recent Visitors
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
                                            <div class="bg-success bg-opacity-10 rounded-4 p-3 me-4">
                                                <i
                                                    class="fadeIn animated bx bx-user-circle text-white fs-5 d-flex align-items-center justify-content-center h-100"></i>
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
                                        <i class="fadeIn animated bx bx-user-circle text-muted mb-3"
                                            style="font-size: 4rem;"></i>
                                        <p class="text-muted fw-medium">No visitors found</p>
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

            // chart 1
            var ctx = document.getElementById("chart1").getContext('2d');

            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, '#6078ea');
            gradientStroke1.addColorStop(1, '#17c5ea');

            var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, '#ff8359');
            gradientStroke2.addColorStop(1, '#ffdf40');

            var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, '#f33a06');
            gradientStroke3.addColorStop(1, '#c92f04');

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                    datasets: [{
                            label: 'Employe',
                            data: [{{ $totalEmployees }}, 59, 80, 81, 65, 59, 80, 81, 59, 80, 81, 65],
                            borderColor: gradientStroke1,
                            backgroundColor: gradientStroke1,
                            hoverBackgroundColor: gradientStroke1,
                            pointRadius: 0,
                            fill: false,
                            borderRadius: 20,
                            borderWidth: 0
                        },
                        {
                            label: 'Exhibitor',
                            data: [{{ $totalExhibitors }}, 48, 40, 19, 28, 48, 40, 19, 40, 19, 28, 48],
                            borderColor: gradientStroke2,
                            backgroundColor: gradientStroke2,
                            hoverBackgroundColor: gradientStroke2,
                            pointRadius: 0,
                            fill: false,
                            borderRadius: 20,
                            borderWidth: 0
                        },
                        {
                            label: 'Visitor',
                            data: [{{ $totalVisitors }}, 48, 40, 19, 28, 48, 40, 19, 40, 19, 28, 48],
                            borderColor: gradientStroke3,
                            backgroundColor: gradientStroke3,
                            hoverBackgroundColor: gradientStroke3,
                            pointRadius: 0,
                            fill: false,
                            borderRadius: 20,
                            borderWidth: 0
                        }
                    ]
                },

                options: {
                    maintainAspectRatio: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.8,
                    plugins: {
                        legend: {
                            display: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // chart 2
            var ctx = document.getElementById("chart2").getContext('2d');

            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, '#fc4a1a');
            gradientStroke1.addColorStop(1, '#f7b733');

            var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, '#4776e6');
            gradientStroke2.addColorStop(1, '#8e54e9');

            var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, '#ee0979');
            gradientStroke3.addColorStop(1, '#ff6a00');

            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Employe", "Exhibitor", "Visitor"],
                    datasets: [{
                        backgroundColor: [
                            gradientStroke1,
                            gradientStroke2,
                            gradientStroke3,
                        ],
                        hoverBackgroundColor: [
                            gradientStroke1,
                            gradientStroke2,
                            gradientStroke3,
                        ],
                        data: [{{ $totalEmployees }}, {{ $totalExhibitors }},
                            {{ $totalVisitors }}
                        ],
                        borderWidth: [1, 1, 1, 1]
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: 82,
                    plugins: {
                        legend: {
                            display: false,
                        }
                    }

                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            new PerfectScrollbar(".recent-employees-list");
            new PerfectScrollbar(".recent-exhibitors-list");
        });

    </script>
@endsection
