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
                        <h4 class="mb-2">155+</h4>
                        <span class="text-gray-600">Completed Courses</span>
                        <div class="flex-between gap-8 mt-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-600 text-white text-2xl"><i class="ph-fill ph-book-open"></i></span>
                            <div id="complete-course" class="remove-tooltip-title rounded-tooltip-value"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">39+</h4>
                        <span class="text-gray-600">Earned Certificate</span>
                        <div class="flex-between gap-8 mt-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-two-600 text-white text-2xl"><i class="ph-fill ph-certificate"></i></span>
                            <div id="earned-certificate" class="remove-tooltip-title rounded-tooltip-value"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">25+</h4>
                        <span class="text-gray-600">Course in Progress</span>
                        <div class="flex-between gap-8 mt-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-purple-600 text-white text-2xl"> <i class="ph-fill ph-graduation-cap"></i></span>
                            <div id="course-progress" class="remove-tooltip-title rounded-tooltip-value"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">18k+</h4>
                        <span class="text-gray-600">Community Support</span>
                        <div class="flex-between gap-8 mt-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-600 text-white text-2xl"><i class="ph-fill ph-users-three"></i></span>
                            <div id="community-support" class="remove-tooltip-title rounded-tooltip-value"></div>
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