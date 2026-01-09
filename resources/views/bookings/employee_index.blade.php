@extends('layouts.app_layout')

@section('title', 'My Bookings & Revenue')

@section('style')
    <link rel="stylesheet" href="{{ asset('/assets/css/enhanced-tables.css') }}">
    <style>
        .revenue-summary-card {
            border-radius: 12px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        
        .revenue-summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(30, 58, 138, 0.12);
        }
    </style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">My Bookings & Revenue</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active">My Bookings</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Revenue Summary Cards -->
            <div class="row g-3 mb-4">
                @php
                    $employeeId = auth()->id();
                    $totalBookings = \App\Models\Booking::where('employee_id', $employeeId)->count();
                    $totalRevenue = \App\Models\Booking::where('employee_id', $employeeId)->sum('amount_paid') ?? 0;
                    $paidRevenue = \App\Models\Booking::where('employee_id', $employeeId)->where('amount_status', 'paid')->sum('amount_paid') ?? 0;
                    $pendingRevenue = \App\Models\Booking::where('employee_id', $employeeId)->where('amount_status', 'pending')->sum('price') ?? 0;
                    $partialRevenue = \App\Models\Booking::where('employee_id', $employeeId)->where('amount_status', 'partial')->sum('amount_paid') ?? 0;
                    $totalPrice = \App\Models\Booking::where('employee_id', $employeeId)->sum('price') ?? 0;
                @endphp
                
                <div class="col-md-3">
                    <div class="card revenue-summary-card" style="border-left-color: #3b82f6;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Bookings</h6>
                                    <h3 class="mb-0">{{ $totalBookings }}</h3>
                                </div>
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bx bx-calendar-check fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card revenue-summary-card" style="border-left-color: #22c55e;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Revenue</h6>
                                    <h3 class="mb-0 text-success">₹{{ number_format($totalRevenue, 2) }}</h3>
                                </div>
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bx bx-rupee fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card revenue-summary-card" style="border-left-color: #22c55e;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Paid Amount</h6>
                                    <h3 class="mb-0 text-success">₹{{ number_format($paidRevenue, 2) }}</h3>
                                </div>
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bx bx-wallet fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card revenue-summary-card" style="border-left-color: #f59e0b;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Pending Revenue</h6>
                                    <h3 class="mb-0" style="color: #f59e0b;">₹{{ number_format($pendingRevenue, 2) }}</h3>
                                </div>
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bx bx-time fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-calendar-check me-2" style="color: var(--sidebar-end, #3b82f6);"></i>All My Bookings
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="bookingsTable" class="table table-hover" style="width:100%">
                            <thead class="table-header-gradient">
                                <tr>
                                    <th><i class="bx bx-hash"></i> #</th>
                                    <th><i class="bx bx-calendar"></i> Booking Date</th>
                                    <th><i class="bx bx-building"></i> Exhibitor</th>
                                    <th><i class="bx bx-user"></i> Visitor</th>
                                    <th><i class="bx bx-map"></i> Location</th>
                                    <th><i class="bx bx-table"></i> Table</th>
                                    <th class="text-end"><i class="bx bx-rupee"></i> Total Price</th>
                                    <th class="text-end"><i class="bx bx-wallet"></i> Amount Paid</th>
                                    <th class="text-end"><i class="bx bx-calculator"></i> Balance</th>
                                    <th class="text-center"><i class="bx bx-info-circle"></i> Status</th>
                                    <th class="text-center"><i class="bx bx-cog"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#bookingsTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ route("employee.bookings.list") }}',
                    type: 'POST',
                    dataSrc: 'data',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { 
                        data: null, 
                        render: function(data, type, row, meta) { 
                            return meta.row + 1; 
                        } 
                    },
                    { data: 'booking_date' },
                    { data: 'exhibitor' },
                    { data: 'visitor' },
                    { data: 'location' },
                    { data: 'table' },
                    { 
                        data: 'price', 
                        className: 'text-end',
                        render: function(data) {
                            return '<strong>₹' + data + '</strong>';
                        }
                    },
                    { 
                        data: 'amount_paid', 
                        className: 'text-end',
                        render: function(data) {
                            return '<strong class="text-success">₹' + data + '</strong>';
                        }
                    },
                    { 
                        data: 'balance', 
                        className: 'text-end',
                        render: function(data) {
                            const balance = parseFloat(data.replace(/,/g, ''));
                            const colorClass = balance > 0 ? 'text-danger' : 'text-success';
                            return '<strong class="' + colorClass + '">₹' + data + '</strong>';
                        }
                    },
                    { 
                        data: 'amount_status', 
                        className: 'text-center',
                        render: function(data) {
                            const badgeClass = data === 'paid' ? 'bg-success' : (data === 'partial' ? 'bg-warning' : 'bg-danger');
                            return '<span class="badge ' + badgeClass + '">' + data.toUpperCase() + '</span>';
                        }
                    },
                    { 
                        data: 'id', 
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            if (row.conversation_id) {
                                return '<a href="{{ route("conversations.invoice", "") }}/' + row.conversation_id + '" class="btn btn-sm btn-action btn-success" title="Generate Invoice" target="_blank">' +
                                       '<i class="bx bx-file"></i> Invoice' +
                                       '</a>';
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    }
                ],
                order: [[1, 'desc']],
                pageLength: 25,
                responsive: true
            });
        });
    </script>
@endsection

