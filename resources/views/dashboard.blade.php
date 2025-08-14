@extends("layout.master")
@section('title','Paridhan-Dashboard Pannel')

@section('content')
<div class="row gy-4 mb-5">
    <div class="col-lg-12">
        <!-- Widgets Start -->
        <div class="row gy-4">
            <div class="col-xxl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">{{ $employeeStats['total'] ?? 0 }}</h4>
                        <span class="text-gray-600">Total Employees</span>
                        <div class="flex-between gap-8 mt-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-600 text-white text-2xl"><i class="ph-fill ph-users"></i></span>
                            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-main">View All</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">{{ $employeeStats['active'] ?? 0 }}</h4>
                        <span class="text-gray-600">Active Employees</span>
                        <div class="flex-between gap-8 mt-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-success-600 text-white text-2xl"><i class="ph-fill ph-user-check"></i></span>
                            <a href="{{ route('employees.index', ['status' => 'active']) }}" class="btn btn-sm btn-success">View Active</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">{{ $employeeStats['inactive'] ?? 0 }}</h4>
                        <span class="text-gray-600">Inactive Employees</span>
                        <div class="flex-between gap-8 mt-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-600 text-white text-2xl"> <i class="ph-fill ph-user-minus"></i></span>
                            <a href="{{ route('employees.index', ['status' => 'inactive']) }}" class="btn btn-sm btn-warning">View Inactive</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">Quick Actions</h4>
                        <span class="text-gray-600">Employee Management</span>
                        <div class="flex-between gap-8 mt-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-info-600 text-white text-2xl"><i class="ph-fill ph-plus"></i></span>
                            <a href="{{ route('employees.create') }}" class="btn btn-sm btn-info">Add Employee</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Widgets End -->

        <!-- Top Course Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">Study Statistics</h4>
                    <div class="flex-align gap-16 flex-wrap">
                        <div class="flex-align flex-wrap gap-16">
                            <div class="flex-align flex-wrap gap-8">
                                <span class="w-8 h-8 rounded-circle bg-main-600"></span>
                                <span class="text-13 text-gray-600">Study</span>
                            </div>
                            <div class="flex-align flex-wrap gap-8">
                                <span class="w-8 h-8 rounded-circle bg-main-two-600"></span>
                                <span class="text-13 text-gray-600">Test</span>
                            </div>
                        </div>
                        <select class="form-select form-control text-13 px-8 pe-24 py-8 rounded-8 w-auto">
                            <option value="1">Yearly</option>
                            <option value="1">Monthly</option>
                            <option value="1">Weekly</option>
                            <option value="1">Today</option>
                        </select>
                    </div>
                </div>

                <div id="doubleLineChart" class="tooltip-style y-value-left"></div>

            </div>
        </div>
        <!-- Top Course End -->

       
    </div>
</div>
@endsection