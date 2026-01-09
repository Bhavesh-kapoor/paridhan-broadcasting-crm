@extends('layouts.app_layout')

@section('title', 'All Bookings & Revenue')

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
                <div class="breadcrumb-title pe-3">All Bookings & Revenue</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active">All Bookings</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Revenue Summary Cards -->
            <div class="row g-3 mb-4">
                @php
                    $totalBookings = \App\Models\Booking::count();
                    $totalRevenue = \App\Models\Booking::sum('amount_paid') ?? 0;
                    $paidRevenue = \App\Models\Booking::where('amount_status', 'paid')->sum('amount_paid') ?? 0;
                    $pendingRevenue = \App\Models\Booking::where('amount_status', 'pending')->sum('price') ?? 0;
                    $partialRevenue = \App\Models\Booking::where('amount_status', 'partial')->sum('amount_paid') ?? 0;
                    $totalPrice = \App\Models\Booking::sum('price') ?? 0;
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="bx bx-calendar-check me-2" style="color: var(--sidebar-end, #3b82f6);"></i>All Bookings
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                            <i class="bx bx-filter me-1"></i>Filters
                        </button>
                    </div>
                    
                    <!-- Filters Section -->
                    <div class="collapse" id="filtersCollapse">
                        <div class="row g-3 pb-3">
                            <div class="col-md-3">
                                <label class="form-label small">Status</label>
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">All Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="partial">Partial</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Date From</label>
                                <input type="date" class="form-control form-control-sm" id="filterDateFrom">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Date To</label>
                                <input type="date" class="form-control form-control-sm" id="filterDateTo">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Employee</label>
                                <select class="form-select form-select-sm" id="filterEmployee">
                                    <option value="">All Employees</option>
                                    @foreach(\App\Models\User::where('role', 'employee')->get() as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-sm btn-primary" onclick="applyFilters()">
                                    <i class="bx bx-check me-1"></i>Apply Filters
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                                    <i class="bx bx-x me-1"></i>Clear
                                </button>
                            </div>
                        </div>
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
                                    <th><i class="bx bx-user-circle"></i> Employee</th>
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

    <!-- Settle Amount Modal -->
    <div class="modal fade" id="settleAmountModal" tabindex="-1" aria-labelledby="settleAmountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-white">
                    <h5 class="modal-title" id="settleAmountModalLabel">
                        <i class="bx bx-wallet me-2"></i>Settle Remaining Amount
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="settleAmountForm">
                    <div class="modal-body">
                        <input type="hidden" id="settle_booking_id" name="booking_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Total Price</label>
                            <input type="text" class="form-control bg-light" id="settle_total_price" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Amount Paid</label>
                            <input type="text" class="form-control bg-light" id="settle_amount_paid" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Remaining Balance</label>
                            <input type="text" class="form-control bg-light fw-bold text-danger" id="settle_remaining_balance" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Amount to Settle <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="settle_amount" name="amount" step="0.01" min="0.01" required>
                            <small class="text-muted">Enter the amount you want to settle (max: remaining balance)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" id="settle_payment_method" name="payment_method">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="online">Online Payment</option>
                                <option value="cheque">Cheque</option>
                                <option value="upi">UPI</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="settle_notes" name="notes" rows="2" placeholder="Optional notes about this payment"></textarea>
                        </div>
                        
                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            <small>The remaining amount will be added to the paid amount. If full amount is settled, status will be updated to "Paid".</small>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">
                                    <i class="bx bx-history me-2"></i>Payment History
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="loadPaymentHistory()">
                                    <i class="bx bx-refresh"></i> Refresh
                                </button>
                            </div>
                            <div id="paymentHistoryContainer" style="max-height: 200px; overflow-y: auto;">
                                <div class="text-center text-muted py-3">
                                    <i class="bx bx-loader bx-spin"></i> Loading...
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bx bx-check me-1"></i>Settle Amount
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let bookingsTable;
        
        $(document).ready(function() {
            bookingsTable = $('#bookingsTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ route("admin.bookings.list") }}',
                    type: 'POST',
                    data: function(d) {
                        return {
                            status: $('#filterStatus').val(),
                            employee_id: $('#filterEmployee').val(),
                            date_from: $('#filterDateFrom').val(),
                            date_to: $('#filterDateTo').val()
                        };
                    },
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
                    { data: 'employee' },
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
                            let buttons = '';
                            
                            // View button - always show
                            buttons += '<a href="{{ route("bookings.invoice", ":id") }}'.replace(':id', row.id) + '" target="_blank" class="btn btn-sm btn-action btn-primary me-1" title="View Details">' +
                                      '<i class="bx bx-show"></i> View' +
                                      '</a>';
                            
                            // Check if booking is already released
                            const isReleased = row.released_at !== null && row.released_at !== '';
                            
                            if (isReleased) {
                                buttons += '<span class="badge bg-secondary me-2">Released</span>';
                                buttons += '<small class="text-muted">' + (row.released_at_formatted || '') + '</small>';
                            } else {
                                // Settle button - only show if not released
                                const balance = parseFloat(row.balance.replace(/,/g, ''));
                                if (balance > 0) {
                                    buttons += '<button onclick="openSettleModal(\'' + row.id + '\', \'' + parseFloat(row.price.replace(/,/g, '')) + '\', \'' + parseFloat(row.amount_paid.replace(/,/g, '')) + '\', \'' + balance + '\')" class="btn btn-sm btn-action btn-warning me-1" title="Settle Amount">' +
                                              '<i class="bx bx-wallet"></i> Settle' +
                                              '</button>';
                                }
                                
                                // Release button - only show if not released
                                buttons += '<button onclick="releaseTable(\'' + row.id + '\')" class="btn btn-sm btn-action btn-danger" title="Release Table">' +
                                          '<i class="bx bx-x"></i> Release' +
                                          '</button>';
                            }
                            
                            return buttons || '<span class="text-muted">-</span>';
                        }
                    }
                ],
                order: [[1, 'desc']],
                pageLength: 25,
                responsive: true
            });

            // Handle settle amount form submission
            $('#settleAmountForm').on('submit', function(e) {
                e.preventDefault();
                
                const bookingId = $('#settle_booking_id').val();
                const amount = parseFloat($('#settle_amount').val());
                const remainingBalance = parseFloat($('#settle_remaining_balance').val().replace(/,/g, '').replace('₹', ''));
                
                if (amount <= 0) {
                    alert('Please enter a valid amount greater than 0.');
                    return;
                }
                
                if (amount > remainingBalance) {
                    alert('Amount cannot exceed the remaining balance.');
                    return;
                }
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalHtml = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Settling...');
                
                $.ajax({
                    url: '{{ route("admin.bookings.settle") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        booking_id: bookingId,
                        amount: amount,
                        payment_method: $('#settle_payment_method').val(),
                        notes: $('#settle_notes').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close modal
                            $('#settleAmountModal').modal('hide');
                            
                            // Reset form
                            $('#settleAmountForm')[0].reset();
                            
                            // Reload table
                            bookingsTable.ajax.reload(null, false);
                            
                            // Reload payment history
                            loadPaymentHistory();
                            
                            // Show success message
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Amount settled successfully!');
                            } else {
                                alert(response.message || 'Amount settled successfully!');
                            }
                        } else {
                            alert(response.message || 'Failed to settle amount. Please try again.');
                            submitBtn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                        alert(errorMessage);
                        submitBtn.prop('disabled', false).html(originalHtml);
                    }
                });
            });
        });
        
        function openSettleModal(bookingId, totalPrice, amountPaid, remainingBalance) {
            const totalPriceNum = parseFloat(totalPrice);
            const amountPaidNum = parseFloat(amountPaid);
            const remainingBalanceNum = parseFloat(remainingBalance);
            
            $('#settle_booking_id').val(bookingId);
            $('#settle_total_price').val('₹' + totalPriceNum.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#settle_amount_paid').val('₹' + amountPaidNum.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#settle_remaining_balance').val('₹' + remainingBalanceNum.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#settle_amount').val('');
            $('#settle_amount').attr('max', remainingBalanceNum);
            $('#settle_amount').attr('step', '0.01');
            $('#settle_payment_method').val('cash');
            $('#settle_notes').val('');
            
            // Load payment history
            window.currentBookingId = bookingId;
            loadPaymentHistory();
            
            $('#settleAmountModal').modal('show');
            
            // Focus on amount input
            setTimeout(function() {
                $('#settle_amount').focus();
            }, 300);
        }
        
        function loadPaymentHistory() {
            const bookingId = $('#settle_booking_id').val() || window.currentBookingId;
            
            if (!bookingId) {
                $('#paymentHistoryContainer').html('<div class="text-center text-muted py-3">No booking selected</div>');
                return;
            }
            
            $('#paymentHistoryContainer').html('<div class="text-center text-muted py-3"><i class="bx bx-loader bx-spin"></i> Loading...</div>');
            
            const routeUrl = '{{ route("admin.bookings.payment-history", ":id") }}'.replace(':id', bookingId);
            
            $.ajax({
                url: routeUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '<div class="list-group list-group-flush">';
                        response.data.forEach(function(payment) {
                            html += '<div class="list-group-item px-0 py-2 border-bottom">';
                            html += '<div class="d-flex justify-content-between align-items-start">';
                            html += '<div class="flex-grow-1">';
                            html += '<div class="d-flex align-items-center mb-1">';
                            html += '<span class="badge bg-success me-2">₹' + payment.amount + '</span>';
                            html += '<span class="text-muted small">' + payment.payment_method + '</span>';
                            html += '</div>';
                            html += '<div class="small text-muted">';
                            html += 'Before: ₹' + payment.amount_before + ' → After: ₹' + payment.amount_after;
                            html += '</div>';
                            if (payment.notes) {
                                html += '<div class="small text-muted mt-1"><i class="bx bx-note me-1"></i>' + payment.notes + '</div>';
                            }
                            html += '</div>';
                            html += '<div class="text-end">';
                            html += '<div class="small text-muted">' + payment.payment_date + '</div>';
                            html += '<div class="small text-muted">by ' + payment.recorded_by + '</div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        });
                        html += '</div>';
                        $('#paymentHistoryContainer').html(html);
                    } else {
                        $('#paymentHistoryContainer').html('<div class="text-center text-muted py-3"><i class="bx bx-info-circle me-2"></i>No payment history found</div>');
                    }
                },
                error: function(xhr) {
                    $('#paymentHistoryContainer').html('<div class="text-center text-danger py-3">Failed to load payment history</div>');
                }
            });
        });
        
        function applyFilters() {
            bookingsTable.ajax.reload();
        }
        
        function clearFilters() {
            $('#filterStatus').val('');
            $('#filterEmployee').val('');
            $('#filterDateFrom').val('');
            $('#filterDateTo').val('');
            bookingsTable.ajax.reload();
        }
        
        function releaseTable(bookingId) {
            if (!confirm('Are you sure you want to release this table? This action cannot be undone.')) {
                return;
            }
            
            $.ajax({
                url: '{{ route("admin.bookings.release", ":id") }}'.replace(':id', bookingId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        bookingsTable.ajax.reload(null, false);
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Table released successfully!');
                        } else {
                            alert(response.message || 'Table released successfully!');
                        }
                    } else {
                        alert(response.message || 'Failed to release table.');
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                    alert(errorMessage);
                }
            });
        }
    </script>
@endsection

