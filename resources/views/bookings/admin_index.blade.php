@extends('layouts.app_layout')

@section('title', 'All Bookings & Revenue')

@section('style')
    <link rel="stylesheet" href="{{ asset('/assets/css/enhanced-tables.css') }}">
    <style>
        .revenue-summary-card {
            border-radius: 12px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 2px 8px rgba(236, 38, 143, 0.08);
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .revenue-summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(236, 38, 143, 0.12);
        }

        /* Table Scrollable Container */
        .table-responsive {
            position: relative;
        }

        .table-responsive::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #ec268f 0%, #f06292 100%);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #c2185b 0%, #ec268f 100%);
        }

        /* DataTables Header Styling */
        .dataTables_scrollHead {
            background: linear-gradient(135deg, #ec268f 0%, #f06292 100%) !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
        }
        
        .dataTables_scrollHeadInner {
            background: transparent !important;
            width: 100% !important;
        }
        
        .dataTables_scrollHeadInner table {
            margin-bottom: 0 !important;
            width: 100% !important;
        }
        
        .dataTables_scrollHeadInner th {
            color: white !important;
            background: transparent !important;
            border-bottom: none !important;
            font-weight: 600;
            white-space: nowrap;
            padding: 12px 8px !important;
        }

        /* Sticky Header */
        .table-header-gradient {
            background: linear-gradient(135deg, #ec268f 0%, #f06292 100%) !important;
            color: white !important;
        }

        /* DataTables Scroll Container */
        .dataTables_scrollBody {
            max-height: calc(100vh - 350px) !important;
            overflow-x: auto !important;
        }

        /* Ensure header scrolls with content */
        .dataTables_wrapper .dataTables_scroll {
            position: relative;
        }
        
        .dataTables_scrollHead {
            position: relative !important;
        }
        
        /* Sync scroll between header and body */
        .dataTables_scrollHead table,
        .dataTables_scrollBody table {
            border-collapse: separate;
            border-spacing: 0;
        }

        /* Table Cell Padding */
        #bookingsTable td {
            padding: 10px 8px !important;
            white-space: nowrap;
            vertical-align: middle;
        }

        /* Action Buttons */
        .btn-action {
            padding: 4px 8px;
            font-size: 0.75rem;
            margin: 2px;
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                        class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active">All Bookings</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Filters Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-filter me-2"></i>Filters
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                            <i class="bx bx-chevron-down me-1"></i>Toggle Filters
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="collapse show" id="filtersCollapse">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label small">Status</label>
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">All Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="partial">Partial</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Campaign</label>
                                <select class="form-select form-select-sm" id="filterCampaign">
                                    <option value="">All Campaigns</option>
                                    @foreach($campaigns as $campaign)
                                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Employee</label>
                                <select class="form-select form-select-sm" id="filterEmployee">
                                    <option value="">All Employees</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Min Amount</label>
                                <input type="number" class="form-control form-control-sm" id="filterMinPrice" placeholder="Min Price">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Max Amount</label>
                                <input type="number" class="form-control form-control-sm" id="filterMaxPrice" placeholder="Max Price">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Min Balance</label>
                                <input type="number" class="form-control form-control-sm" id="filterMinBalance" placeholder="Min Balance">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Max Balance</label>
                                <input type="number" class="form-control form-control-sm" id="filterMaxBalance" placeholder="Max Balance">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Date From</label>
                                <input type="date" class="form-control form-control-sm" id="filterDateFrom">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Date To</label>
                                <input type="date" class="form-control form-control-sm" id="filterDateTo">
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
            </div>

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
                                    <h3 class="mb-0" id="cardTotalBookings">{{ $totalBookings }}</h3>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
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
                                    <h3 class="mb-0 text-success" id="cardTotalRevenue">₹{{ number_format($totalRevenue, 2) }}</h3>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
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
                                    <h3 class="mb-0 text-success" id="cardPaidRevenue">₹{{ number_format($paidRevenue, 2) }}</h3>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
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
                                    <h3 class="mb-0" style="color: #f59e0b;" id="cardPendingRevenue">₹{{ number_format($pendingRevenue, 2) }}</h3>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bx bx-time fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $totalBalance = $totalPrice - $totalRevenue;
                    $remainingBalance = \App\Models\Booking::whereNull('released_at')
                        ->get()
                        ->sum(function ($b) {
                            return ($b->price ?? 0) - ($b->amount_paid ?? 0); });
                    $remainingCount = \App\Models\Booking::whereNull('released_at')
                        ->whereRaw('(price - COALESCE(amount_paid, 0)) > 0')
                        ->count();
                @endphp
                
                <div class="col-md-3">
                    <div class="card revenue-summary-card" style="border-left-color: #8b5cf6;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Amount</h6>
                                    <h3 class="mb-0" style="color: #8b5cf6;" id="cardTotalPrice">₹{{ number_format($totalPrice, 2) }}</h3>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bx bx-money fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card revenue-summary-card" style="border-left-color: #f97316;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Balance</h6>
                                    <h3 class="mb-0" style="color: #f97316;" id="cardTotalBalance">₹{{ number_format($totalBalance, 2) }}</h3>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bx bx-calculator fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card revenue-summary-card" style="border-left-color: #ef4444; cursor: pointer;"
                        onclick="openRemainingBalanceModal()">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Remaining Balance</h6>
                                    <h3 class="mb-0" style="color: #ef4444;" id="cardRemainingBalance">₹{{ number_format($remainingBalance, 2) }}</h3>
                                    <small class="text-muted" id="cardRemainingCount">{{ $remainingCount }} booking(s)</small>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bx bx-money fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bx bx-calendar-check me-2" style="color: var(--sidebar-end, #3b82f6);"></i>All Bookings
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive"
                        style="max-height: calc(100vh - 350px); overflow-y: auto; overflow-x: auto;">
                        <table id="bookingsTable" class="table table-hover table-striped mb-0"
                            style="width:100%; min-width: 1200px;">
                            <thead class="table-header-gradient"
                                style="background: #ec268f; background: linear-gradient(135deg, #ec268f 0%, #f06292 100%); color: white;">
                                <tr>
                                    <th style="min-width: 50px; color: white !important;"><i class="bx bx-hash"></i> #</th>
                                    <th style="min-width: 120px; color: white !important;"><i class="bx bx-calendar"></i> Booking Date</th>
                                    <th style="min-width: 150px; color: white !important;"><i class="bx bx-building"></i> Exhibitor</th>
                                    <th style="min-width: 150px; color: white !important;"><i class="bx bx-user"></i> Visitor</th>
                                    <th style="min-width: 120px; color: white !important;"><i class="bx bx-user-circle"></i> Employee</th>
                                    <th style="min-width: 150px; color: white !important;"><i class="bx bx-megaphone"></i> Campaign Name</th>
                                    <th style="min-width: 150px; color: white !important;"><i class="bx bx-map-pin"></i> Location / Table</th>
                                    <th class="text-end" style="min-width: 100px; color: white !important;"><i class="bx bx-rupee"></i> Total Amount
                                    </th>
                                    <th class="text-end" style="min-width: 100px; color: white !important;"><i class="bx bx-wallet"></i> Amount Paid
                                    </th>
                                    <th class="text-end" style="min-width: 100px; color: white !important;"><i class="bx bx-calculator"></i> Remaining Amount
                                    </th>
                                    <th class="text-center" style="min-width: 100px; color: white !important;"><i class="bx bx-info-circle"></i>
                                        Status</th>
                                    <th class="text-center" style="min-width: 200px; color: white !important;"><i class="bx bx-cog"></i> Actions</th>
                                </tr>
                            </thead>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="7" class="text-end">Total:</td>
                                    <td class="text-end" id="footerTotalAmount">₹0.00</td>
                                    <td class="text-end" id="footerAmountPaid">₹0.00</td>
                                    <td class="text-end" id="footerRemainingAmount">₹0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
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
    <div class="modal fade" id="settleAmountModal" tabindex="-1" aria-labelledby="settleAmountModalLabel"
        aria-hidden="true">
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
                            <input type="text" class="form-control bg-light fw-bold text-danger"
                                id="settle_remaining_balance" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount to Settle <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="settle_amount" name="amount" step="0.01"
                                min="0.01" required>
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
                            <textarea class="form-control" id="settle_notes" name="notes" rows="2"
                                placeholder="Optional notes about this payment"></textarea>
                        </div>

                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            <small>The remaining amount will be added to the paid amount. If full amount is settled, status
                                will be updated to "Paid".</small>
                        </div>

                        <hr>

                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">
                                    <i class="bx bx-history me-2"></i>Payment History
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="loadPaymentHistory()">
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

    <!-- Remaining Balance Modal -->
    <div class="modal fade" id="remainingBalanceModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bx bx-money me-2"></i>Remaining Balance Bookings
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label small">Filter by Campaign</label>
                            <select class="form-select form-select-sm" id="rbFilterCampaign">
                                <option value="">All Campaigns</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Filter by Employee</label>
                            <select class="form-select form-select-sm" id="rbFilterEmployee">
                                <option value="">All Employees</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Search</label>
                            <input type="text" class="form-control form-control-sm" id="rbSearch"
                                placeholder="Search by name...">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-primary me-2" onclick="loadRemainingBalance()">
                                <i class="bx bx-search me-1"></i>Search
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearRbFilters()">
                                <i class="bx bx-x me-1"></i>Clear
                            </button>
                        </div>
                    </div>
                    <div id="remainingBalanceLoader" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="remainingBalanceContent">
                        <div class="table-responsive">
                            <table class="table table-hover" id="remainingBalanceTable">
                                <caption class="text-start px-3 py-2 fw-semibold bg-light border-bottom">
                                    <i class="bx bx-calendar-check me-2 text-primary"></i>
                                    All Bookings List
                                </caption>

                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Exhibitor</th>
                                        <th>Visitor</th>
                                        <th>Employee</th>
                                        <th>Campaign</th>
                                        <th>Location</th>
                                        <th>Price</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="remainingBalanceTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                                <tfoot id="remainingBalanceTableFooter">
                                    <!-- Total will be shown here -->
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let bookingsTable;

        $(document).ready(function () {
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const campaignId = urlParams.get('campaign_id');
            const minBalance = urlParams.get('min_balance');
            
            // Initialize filters from URL parameters
            if (campaignId) {
                $('#filterCampaign').val(campaignId);
            }
            if (minBalance) {
                $('#filterMinBalance').val(minBalance);
            }
            
            // Initialize cards
            updateCards();
            
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#bookingsTable')) {
                $('#bookingsTable').DataTable().destroy();
                $('#bookingsTable').empty();
            }

            bookingsTable = $('#bookingsTable').DataTable({
                processing: true,
                serverSide: false,
                autoWidth: false,
                scrollX: true,
                scrollY: 'calc(100vh - 350px)',
                scrollCollapse: false,
                fixedHeader: false,
                responsive: false,
                paging: true,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                drawCallback: function() {
                    // Sync header scroll with body scroll
                    $('.dataTables_scrollBody').on('scroll', function() {
                        $('.dataTables_scrollHead').scrollLeft($(this).scrollLeft());
                    });
                    $('.dataTables_scrollHead').on('scroll', function() {
                        $('.dataTables_scrollBody').scrollLeft($(this).scrollLeft());
                    });
                },
                ajax: {
                    url: '{{ route("admin.bookings.list") }}',
                    type: 'POST',
                    data: function (d) {
                        return {
                            status: $('#filterStatus').val() || '',
                            campaign_id: $('#filterCampaign').val() || '',
                            employee_id: $('#filterEmployee').val() || '',
                            min_price: $('#filterMinPrice').val() || '',
                            max_price: $('#filterMaxPrice').val() || '',
                            min_balance: $('#filterMinBalance').val() || '',
                            max_balance: $('#filterMaxBalance').val() || '',
                            date_from: $('#filterDateFrom').val() || '',
                            date_to: $('#filterDateTo').val() || ''
                        };
                    },
                    dataSrc: function (json) {
                        console.log('AJAX Response:', json);
                        if (json && json.data) {
                            console.log('Data array length:', json.data.length);
                            return json.data;
                        }
                        console.error('No data array found in response');
                        return [];
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function (xhr, error, thrown) {
                        console.error('DataTable Error:', error, thrown);
                        console.error('Response:', xhr.responseText);
                        console.error('Status:', xhr.status);
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            console.error('Error Message:', xhr.responseJSON.error);
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Failed to load bookings: ' + (xhr.responseJSON?.error || error));
                        } else {
                            alert('Failed to load bookings: ' + (xhr.responseJSON?.error || error));
                        }
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'sl_no',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'booking_date',
                        name: 'booking_date',
                        defaultContent: '-'
                    },
                    {
                        data: 'exhibitor',
                        name: 'exhibitor',
                        defaultContent: '-'
                    },
                    {
                        data: 'visitor',
                        name: 'visitor',
                        defaultContent: '-'
                    },
                    {
                        data: 'employee',
                        name: 'employee',
                        defaultContent: '-'
                    },
                    {
                        data: 'campaign',
                        name: 'campaign',
                        defaultContent: '-',
                        render: function (data, type, row) {
                            if (!data || data === '-') return '<span class="text-muted">-</span>';
                            return '<span class="badge bg-info">' + data + '</span>';
                        }
                    },
                    {
                        data: null,
                        name: 'location_table',
                        orderable: false,
                        render: function (data, type, row) {
                            var loc = row.location || '-';
                            var tbl = row.table || '-';
                            return '<div class="d-flex flex-column">' +
                                   '<span class="mb-1"><i class="bx bx-map text-primary me-1"></i>' + loc + '</span>' +
                                   '<span><i class="bx bx-table text-info me-1"></i>' + tbl + '</span>' +
                                   '</div>';
                        }
                    },
                    {
                        data: 'price',
                        name: 'price',
                        className: 'text-end',
                        defaultContent: '-',
                        render: function (data, type, row) {
                            if (!data || data === '-') return '-';
                            return '<strong>₹' + data + '</strong>';
                        }
                    },
                    {
                        data: 'amount_paid',
                        name: 'amount_paid',
                        className: 'text-end',
                        defaultContent: '-',
                        render: function (data, type, row) {
                            if (!data || data === '-') return '-';
                            return '<strong class="text-success">₹' + data + '</strong>';
                        }
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        className: 'text-end',
                        defaultContent: '-',
                        render: function (data, type, row) {
                            if (!data || data === '-') return '-';
                            try {
                                const balance = parseFloat(data.toString().replace(/,/g, ''));
                                const colorClass = balance > 0 ? 'text-danger' : 'text-success';
                                return '<strong class="' + colorClass + '">₹' + data + '</strong>';
                            } catch (e) {
                                return '<strong>₹' + data + '</strong>';
                            }
                        }
                    },
                    {
                        data: 'amount_status',
                        name: 'amount_status',
                        className: 'text-center',
                        defaultContent: '-',
                        render: function (data, type, row) {
                            if (!data || data === '-') return '-';
                            const badgeClass = data === 'paid' ? 'bg-success' : (data === 'partial' ? 'bg-warning' : 'bg-danger');
                            return '<span class="badge ' + badgeClass + '">' + (data ? data.toUpperCase() : 'N/A') + '</span>';
                        }
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        defaultContent: '-',
                        render: function (data, type, row) {
                            if (!row || !row.id) return '<span class="text-muted">-</span>';

                            let buttons = '';

                            // View Invoice button - always show
                            buttons += '<a href="{{ route("bookings.invoice", ":id") }}'.replace(':id', row.id) + '" target="_blank" class="btn btn-sm btn-action btn-primary me-1" title="View Invoice">' +
                                '<i class="bx bx-receipt"></i> Invoice' +
                                '</a>';

                            // Check if booking is already released
                            const isReleased = row.released_at !== null && row.released_at !== '' && row.released_at !== undefined;

                            if (isReleased) {
                                buttons += '<span class="badge bg-secondary me-2">Released</span>';
                                buttons += '<small class="text-muted">' + (row.released_at_formatted || '') + '</small>';
                            } else {
                                // Settle button - only show if not released
                                try {
                                    const balance = row.balance ? parseFloat(row.balance.toString().replace(/,/g, '')) : 0;
                                    if (balance > 0) {
                                        const price = row.price ? parseFloat(row.price.toString().replace(/,/g, '')) : 0;
                                        const amountPaid = row.amount_paid ? parseFloat(row.amount_paid.toString().replace(/,/g, '')) : 0;
                                        buttons += '<button onclick="openSettleModal(\'' + row.id + '\', \'' + price + '\', \'' + amountPaid + '\', \'' + balance + '\')" class="btn btn-sm btn-action btn-warning me-1" title="Settle Amount">' +
                                            '<i class="bx bx-wallet"></i> Settle' +
                                            '</button>';
                                    }
                                } catch (e) {
                                    console.error('Error parsing balance:', e);
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
                language: {
                    emptyTable: "No bookings found",
                    zeroRecords: "No matching bookings found",
                    processing: "Loading bookings...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                initComplete: function (settings, json) {
                    console.log('DataTable initialized successfully');
                    console.log('Data received:', json);
                    if (json && json.data) {
                        console.log('Total records:', json.data.length);
                    }
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    // Helper to parse currency
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[\₹,]/g, '')) :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // Calculate totals
                    var totalAmount = api
                        .column(7, { search: 'applied' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var amountPaid = api
                        .column(8, { search: 'applied' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var remainingBalance = api
                        .column(9, { search: 'applied' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer
                    $(api.column(7).footer()).html('₹' + totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $(api.column(8).footer()).html('₹' + amountPaid.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $(api.column(9).footer()).html('₹' + remainingBalance.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                }
            });

            // Handle settle amount form submission
            $('#settleAmountForm').on('submit', function (e) {
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
                    success: function (response) {
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
                    error: function (xhr) {
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
            $('#settle_total_price').val('₹' + totalPriceNum.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#settle_amount_paid').val('₹' + amountPaidNum.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#settle_remaining_balance').val('₹' + remainingBalanceNum.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
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
            setTimeout(function () {
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
                success: function (response) {
                    if (response.success && response.data.length > 0) {
                        let html = '<div class="list-group list-group-flush">';
                        response.data.forEach(function (payment) {
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
                error: function (xhr) {
                    $('#paymentHistoryContainer').html('<div class="text-center text-danger py-3">Failed to load payment history</div>');
                }
            });
        };


        function applyFilters() {
            bookingsTable.ajax.reload(null, false);
            updateCards();
        }
        
        function updateCards() {
            $.ajax({
                url: '{{ route("admin.bookings.stats") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: $('#filterStatus').val() || '',
                    campaign_id: $('#filterCampaign').val() || '',
                    employee_id: $('#filterEmployee').val() || '',
                    min_price: $('#filterMinPrice').val() || '',
                    max_price: $('#filterMaxPrice').val() || '',
                    min_balance: $('#filterMinBalance').val() || '',
                    max_balance: $('#filterMaxBalance').val() || '',
                    date_from: $('#filterDateFrom').val() || '',
                    date_to: $('#filterDateTo').val() || ''
                },
                success: function(response) {
                    $('#cardTotalBookings').text(response.total_bookings);
                    $('#cardTotalRevenue').text('₹' + response.total_revenue);
                    $('#cardPaidRevenue').text('₹' + response.paid_revenue);
                    $('#cardPendingRevenue').text('₹' + response.pending_revenue);
                    $('#cardTotalPrice').text('₹' + response.total_price);
                    $('#cardTotalBalance').text('₹' + response.total_balance);
                    $('#cardRemainingBalance').text('₹' + response.remaining_balance);
                    $('#cardRemainingCount').text(response.remaining_count + ' booking(s)');
                },
                error: function() {
                    console.error('Failed to update cards');
                }
            });
        }

        function clearFilters() {
            $('#filterStatus').val('');
            $('#filterCampaign').val('');
            $('#filterEmployee').val('');
            $('#filterMinPrice').val('');
            $('#filterMaxPrice').val('');
            $('#filterMinBalance').val('');
            $('#filterMaxBalance').val('');
            $('#filterDateFrom').val('');
            $('#filterDateTo').val('');
            bookingsTable.ajax.reload(null, false);
            updateCards();
        }

        // Remaining Balance Modal Functions
        function openRemainingBalanceModal() {
            $('#remainingBalanceModal').modal('show');
            loadRemainingBalance();
        }

        function loadRemainingBalance() {
            const campaignId = $('#rbFilterCampaign').val() || '';
            const employeeId = $('#rbFilterEmployee').val() || '';
            const search = $('#rbSearch').val() || '';

            $('#remainingBalanceLoader').show();
            $('#remainingBalanceContent').hide();

            $.ajax({
                url: '{{ route("admin.bookings.remainingBalance") }}',
                type: 'GET',
                data: {
                    campaign_id: campaignId,
                    employee_id: employeeId
                },
                success: function (response) {
                    $('#remainingBalanceLoader').hide();
                    $('#remainingBalanceContent').show();

                    if (response.status && response.data && response.data.length > 0) {
                        let html = '';
                        let filteredData = response.data;

                        // Apply search filter
                        if (search) {
                            const searchLower = search.toLowerCase();
                            filteredData = filteredData.filter(function (item) {
                                return (item.exhibitor && item.exhibitor.toLowerCase().includes(searchLower)) ||
                                    (item.visitor && item.visitor.toLowerCase().includes(searchLower)) ||
                                    (item.campaign && item.campaign.toLowerCase().includes(searchLower)) ||
                                    (item.employee && item.employee.toLowerCase().includes(searchLower));
                            });
                        }

                        filteredData.forEach(function (booking) {
                            const badgeClass = booking.amount_status === 'paid' ? 'bg-success' :
                                booking.amount_status === 'partial' ? 'bg-warning' : 'bg-danger';
                            html += `
                                    <tr>
                                        <td>${booking.booking_date}</td>
                                        <td>${booking.exhibitor}</td>
                                        <td>${booking.visitor}</td>
                                        <td>${booking.employee}</td>
                                        <td>${booking.campaign}</td>
                                        <td>${booking.location}</td>
                                        <td>₹${booking.price}</td>
                                        <td>₹${booking.amount_paid}</td>
                                        <td class="fw-bold text-danger">₹${booking.balance}</td>
                                        <td><span class="badge ${badgeClass}">${booking.amount_status.toUpperCase()}</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="openSettleModal('${booking.id}')">
                                                <i class="bx bx-money"></i> Settle
                                            </button>
                                        </td>
                                    </tr>
                                `;
                        });

                        $('#remainingBalanceTableBody').html(html);

                        // Calculate total balance
                        const totalBalance = filteredData.reduce(function (sum, item) {
                            return sum + parseFloat(item.balance.replace(/,/g, ''));
                        }, 0);

                        $('#remainingBalanceTableFooter').html(`
                                <tr class="table-info">
                                    <th colspan="8" class="text-end">Total Remaining Balance:</th>
                                    <th class="text-danger">₹${totalBalance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</th>
                                    <th colspan="2"></th>
                                </tr>
                            `);
                    } else {
                        $('#remainingBalanceTableBody').html('<tr><td colspan="11" class="text-center py-4 text-muted">No bookings with remaining balance found</td></tr>');
                        $('#remainingBalanceTableFooter').html('');
                    }
                },
                error: function () {
                    $('#remainingBalanceLoader').hide();
                    $('#remainingBalanceContent').show();
                    $('#remainingBalanceTableBody').html('<tr><td colspan="11" class="text-center py-4 text-danger">Failed to load data</td></tr>');
                }
            });
        }

        function clearRbFilters() {
            $('#rbFilterCampaign').val('');
            $('#rbFilterEmployee').val('');
            $('#rbSearch').val('');
            loadRemainingBalance();
        }

        // Load remaining balance on modal show
        $('#remainingBalanceModal').on('shown.bs.modal', function () {
            loadRemainingBalance();
        });

        // Search on enter key
        $('#rbSearch').on('keypress', function (e) {
            if (e.which === 13) {
                loadRemainingBalance();
            }
        });

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
                success: function (response) {
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
                error: function (xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                    alert(errorMessage);
                }
            });
        }
    </script>
@endsection