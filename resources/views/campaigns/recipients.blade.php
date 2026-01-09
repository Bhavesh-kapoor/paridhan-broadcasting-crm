@extends('layouts.app_layout')

@section('title', 'Campaign Recipients')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/enhanced-tables.css') }}">
    <style>
        :root {
            --sidebar-start: #1e3a8a;
            --sidebar-end: #3b82f6;
        }
        
        .btn-quick-action {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-quick-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }
        
        /* Horizontal scrollbar for table - always visible */
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            position: relative;
            background-color: #ffffff;
        }
        
        .table-responsive::-webkit-scrollbar {
            height: 10px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 5px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            border-radius: 5px;
            border: 2px solid #f1f5f9;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #475569 0%, #334155 100%);
        }
        
        /* Enhanced Table Design */
        #recipientsTable {
            width: 100% !important;
            min-width: 1000px;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        #recipientsTable thead {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        #recipientsTable thead th {
            white-space: nowrap;
            padding: 16px 18px;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #ffffff;
            border: none;
            vertical-align: middle;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        #recipientsTable thead th:first-child {
            border-top-left-radius: 0;
            padding-left: 24px;
        }
        
        #recipientsTable thead th:last-child {
            border-top-right-radius: 0;
            padding-right: 24px;
        }
        
        #recipientsTable thead th i {
            margin-right: 8px;
            font-size: 1.1rem;
            vertical-align: middle;
            opacity: 0.95;
        }
        
        #recipientsTable thead th.text-center {
            text-align: center !important;
        }
        
        #recipientsTable tbody {
            background-color: #ffffff;
        }
        
        #recipientsTable tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }
        
        #recipientsTable tbody tr:hover {
            background-color: #f8fafc;
            transform: scale(1.001);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        #recipientsTable tbody tr:last-child {
            border-bottom: none;
        }
        
        #recipientsTable tbody td {
            white-space: nowrap;
            padding: 16px 18px;
            font-size: 0.875rem;
            color: #374151;
            vertical-align: middle;
            border: none;
            transition: all 0.2s ease;
        }
        
        #recipientsTable tbody td:first-child {
            padding-left: 24px;
            font-weight: 600;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        #recipientsTable tbody td:last-child {
            padding-right: 24px;
        }
        
        #recipientsTable tbody td.text-center {
            text-align: center !important;
        }
        
        /* Name column styling */
        #recipientsTable tbody td:nth-child(2) {
            font-weight: 600;
            color: #111827;
        }
        
        /* Phone and Email columns */
        #recipientsTable tbody td:nth-child(3),
        #recipientsTable tbody td:nth-child(4) {
            color: #4b5563;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }
        
        /* Badge styling */
        #recipientsTable .badge {
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        /* Action buttons container */
        #recipientsTable .d-flex.gap-1 {
            gap: 8px !important;
        }
        
        /* Table alternating row colors */
        #recipientsTable tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        #recipientsTable tbody tr:nth-child(even):hover {
            background-color: #f3f4f6;
        }
        
        /* Responsive table styles for mobile */
        @media (max-width: 768px) {
            #recipientsTable {
                font-size: 0.85rem;
                min-width: 800px;
            }
            
            #recipientsTable thead th {
                padding: 12px 10px !important;
                font-size: 0.75rem;
            }
            
            #recipientsTable tbody td {
                padding: 12px 10px !important;
                font-size: 0.8rem;
            }
            
            #recipientsTable thead th:first-child {
                padding-left: 12px !important;
            }
            
            #recipientsTable tbody td:first-child {
                padding-left: 12px !important;
            }
            
            .btn-quick-action {
                padding: 0.3rem 0.5rem;
                font-size: 0.75rem;
            }
            
            .btn-quick-action i {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            #recipientsTable {
                min-width: 900px;
            }
        }
        
        /* DataTables responsive child row styling */
        table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before {
            background-color: var(--sidebar-end, #3b82f6);
            border: 2px solid white;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
        }
        
        table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control:before {
            background-color: #22c55e;
        }
        
        /* Responsive child row details */
        .dtr-details {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border-radius: 8px;
            padding: 12px;
            margin: 8px 0;
        }
        
        .dtr-details li {
            padding: 6px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .dtr-details li:last-child {
            border-bottom: none;
        }
        
        .dtr-details .dtr-title {
            font-weight: 600;
            color: var(--sidebar-end, #3b82f6);
            min-width: 120px;
            display: inline-block;
        }
        
        /* Conversations Canvas Card Styling */
        #conversationsList .card {
            transition: all 0.2s ease;
        }
        
        #conversationsList .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }
        
        #conversationsList .card-header {
            font-size: 0.85rem;
        }
        
        #conversationsList .card-body {
            font-size: 0.8rem;
        }
        
        #conversationsList .badge {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
    </style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Campaign Recipients</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('employee.campaigns.index') }}">Campaigns</a></li>
                            <li class="breadcrumb-item active">{{ $campaign->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Campaign Info -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="text-white mb-1">{{ $campaign->name }}</h4>
                                    <p class="text-white-50 mb-0">{{ $campaign->subject }}</p>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('campaigns.conversations', $campaign->id) }}" class="btn btn-light btn-sm">
                                        <i class="bx bx-conversation me-1"></i>View Conversations
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
                            <h5 class="mb-0 fw-bold">
                                <i class="bx bx-user me-2" style="color: var(--sidebar-end);"></i>Campaign Recipients (Visitors)
                            </h5>
                            <small class="text-muted">Click on actions to add conversation or book table for each visitor</small>
                        </div>
                        <div class="card-body p-0" style="background-color: #ffffff;">
                            <div class="table-responsive" style="border-radius: 0 0 12px 12px; overflow: hidden;">
                                <table class="table table-hover mb-0" id="recipientsTable">
                                    <thead>
                                        <tr>
                                            <th><i class="bx bx-hash"></i> #</th>
                                            <th><i class="bx bx-user"></i> Name</th>
                                            <th><i class="bx bx-phone"></i> Phone</th>
                                            <th><i class="bx bx-envelope"></i> Email</th>
                                            <th class="text-center"><i class="bx bx-send"></i> Status</th>
                                            <th class="text-center"><i class="bx bx-conversation"></i> Conversation</th>
                                            <th class="text-center"><i class="bx bx-calendar-check"></i> Booking</th>
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
        </div>
    </div>

    <!-- Quick Conversation Modal -->
    <div class="modal fade" id="quickConversationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title">
                        <i class="bx bx-conversation me-2"></i>Add Conversation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickConversationForm">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}" id="qc_campaign_id">
                        <input type="hidden" name="visitor_id" id="qc_visitor_id">
                        <input type="hidden" name="employee_id" value="{{ auth()->id() }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Visitor (Fixed)</label>
                            <input type="text" class="form-control bg-light" id="qc_visitor_name" readonly style="cursor: not-allowed;">
                        </div>
                        
                        <input type="hidden" name="exhibitor_id" id="qc_exhibitor_id" value="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Location</label>
                            <select name="location_id" id="qc_location_id" class="form-select">
                                <option value="">-- Select Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->loc_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Table <small class="text-muted">(Searchable - handles 2000+ tables)</small></label>
                            <select name="table_id" id="qc_table_id" class="form-select select2-table">
                                <option value="">-- Select Table --</option>
                            </select>
                            <small class="text-muted">Type to search tables. Tables are loaded dynamically.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Outcome <span class="text-danger">*</span></label>
                            <select name="outcome" id="qc_outcome" class="form-select" required>
                                <option value="">-- Select Outcome --</option>
                                <option value="busy">Busy</option>
                                <option value="interested">Interested</option>
                                <option value="materialised">Materialised</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" name="notes" id="qc_notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="bx bx-check me-1"></i>Add Conversation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Booking Modal -->
    <div class="modal fade" id="quickBookingModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title">
                        <i class="bx bx-calendar-check me-2"></i>Book Table
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickBookingForm">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}" id="qb_campaign_id">
                        <input type="hidden" name="visitor_id" id="qb_visitor_id">
                        <input type="hidden" name="employee_id" value="{{ auth()->id() }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Exhibitor Name</label>
                            <input type="text" class="form-control" id="qb_exhibitor_name" readonly>
                        </div>
                        
                        <input type="hidden" name="exhibitor_id" id="qb_exhibitor_id">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                            <select name="location_id" id="qb_location_id" class="form-select" required>
                                <option value="">-- Select Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->loc_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Table <span class="text-danger">*</span></label>
                            <select name="table_id" id="qb_table_id" class="form-select" required>
                                <option value="">-- Select Table --</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Booking Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="booking_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Total Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="price" id="qb_price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Amount Paid</label>
                                <input type="number" step="0.01" class="form-control" name="amount_paid" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                                <select name="amount_status" class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="partial">Partial</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="bx bx-check me-1"></i>Book Table
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Side Conversations Canvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="conversationsCanvas" style="width: 600px;">
        <div class="offcanvas-header border-bottom" style="background: linear-gradient(135deg, var(--sidebar-start, #1e3a8a) 0%, var(--sidebar-end, #3b82f6) 100%); color: white;">
            <div class="flex-grow-1">
                <h5 class="offcanvas-title mb-1" id="canvasContactName">
                    <i class="bx bx-conversation me-2"></i>Conversations
                </h5>
                <small class="text-white-50" id="canvasContactInfo">Loading...</small>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0" style="display: flex; flex-direction: column;">
            <!-- Loading State -->
            <div id="conversationsLoading" class="text-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading conversations...</p>
            </div>

            <!-- Conversations List -->
            <div id="conversationsList" class="flex-grow-1 overflow-auto" style="display: none; max-height: calc(100vh - 200px);">
                <!-- Conversations will be inserted here -->
            </div>

            <!-- Empty State -->
            <div id="conversationsEmpty" class="text-center p-5" style="display: none;">
                <i class="bx bx-conversation fs-1 text-muted"></i>
                <p class="text-muted mt-3">No conversations yet</p>
                <p class="text-muted small">Click "Add Conversation" below to start</p>
            </div>

            <!-- Add Conversation Button (Sticky at bottom) -->
            <div class="border-top p-3 bg-light">
                <button type="button" class="btn btn-primary w-100" id="canvasAddConversationBtn">
                    <i class="bx bx-plus me-2"></i>Add Conversation
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Conversation Modal for Canvas -->
    <div class="modal fade" id="canvasConversationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--sidebar-start, #1e3a8a) 0%, var(--sidebar-end, #3b82f6) 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title">
                        <i class="bx bx-conversation me-2"></i>Add Conversation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="canvasConversationForm">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}" id="canvas_campaign_id">
                        <input type="hidden" name="visitor_id" id="canvas_visitor_id">
                        <input type="hidden" name="employee_id" value="{{ auth()->id() }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Visitor (Fixed)</label>
                            <input type="text" class="form-control bg-light" id="canvas_visitor_name" readonly style="cursor: not-allowed;">
                        </div>
                        
                        <input type="hidden" name="exhibitor_id" id="canvas_exhibitor_id" value="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Location</label>
                            <select name="location_id" id="canvas_location_id" class="form-select" style="width: 100%;">
                                <option value="">-- Select Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->loc_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Table</label>
                            <select name="table_id" id="canvas_table_id" class="form-select" style="width: 100%;">
                                <option value="">-- Select Table --</option>
                            </select>
                            <small class="text-muted">Select location first to load tables</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Outcome <span class="text-danger">*</span></label>
                            <select name="outcome" id="canvas_outcome" class="form-select" required>
                                <option value="">-- Select Outcome --</option>
                                <option value="busy">Busy</option>
                                <option value="interested">Interested</option>
                                <option value="materialised">Materialised</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" name="notes" id="canvas_notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="bx bx-check me-1"></i>Add Conversation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var recipientsTable = $('#recipientsTable').DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                scrollCollapse: false,
                autoWidth: false,
                fixedColumns: false,
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                },
                ajax: {
                    url: '{{ route("employee.campaigns.recipients.list", $campaign->id) }}',
                    type: 'POST',
                    dataSrc: 'data',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { 
                        data: null, 
                        render: function(data, type, row, meta) { return meta.row + 1; },
                        className: 'control'
                    },
                    { data: 'name' },
                    { data: 'phone' },
                    { data: 'email' },
                    { 
                        data: 'status', 
                        className: 'text-center',
                        render: function(data) {
                            const badgeClass = data === 'sent' ? 'bg-success' : (data === 'failed' ? 'bg-danger' : 'bg-warning');
                            return `<span class="badge ${badgeClass}">${data.toUpperCase()}</span>`;
                        }
                    },
                    { 
                        data: 'has_conversation', 
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (data) {
                                return `<span class="badge bg-success"><i class="bx bx-check"></i> ${row.conversation_outcome ? row.conversation_outcome.toUpperCase() : 'YES'}</span>`;
                            }
                            return '<span class="badge bg-secondary">No</span>';
                        }
                    },
                    { 
                        data: 'has_booking', 
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (data || row.booking_id) {
                                return '<span class="badge bg-success"><i class="bx bx-check"></i> Yes</span>';
                            }
                            return '<span class="badge bg-secondary">No Booking</span>';
                        }
                    },
                    { 
                        data: 'id', 
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            let actions = '<div class="d-flex gap-1 justify-content-center flex-wrap">';
                            
                            if (!row.has_conversation) {
                                const safeName = (row.name || '').replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, ' ');
                                actions += `<button class="btn btn-sm btn-quick-action btn-primary" onclick="openQuickConversation('${row.contact_id}', '${safeName}')" title="Add Conversation">
                                    <i class="bx bx-conversation"></i> <span class="d-none d-md-inline">Conversation</span>
                                </button>`;
                            }
                            
                            if (!row.has_booking) {
                                const safeName = (row.name || '').replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, ' ');
                                actions += `<button class="btn btn-sm btn-quick-action btn-success" onclick="openQuickBooking('${row.contact_id}', '${safeName}')" title="Book Table">
                                    <i class="bx bx-calendar-check"></i> <span class="d-none d-md-inline">Book</span>
                                </button>`;
                            }
                            
                            // Always show view button if conversation exists or contact has id
                            if (row.contact_id) {
                                const safeName = (row.name || '').replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, ' ');
                                actions += `<button class="btn btn-sm btn-quick-action btn-info" onclick="openConversationsCanvas('${row.contact_id}', '${safeName}', '${row.phone || ''}', '${row.email || ''}')" title="View Conversations">
                                    <i class="bx bx-show"></i> <span class="d-none d-md-inline">View</span>
                                </button>`;
                            }
                            
                            // Show invoice button if booking exists and has conversation
                            if (row.has_invoice && row.conversation_id) {
                                const invoiceUrl = '{{ route("conversations.invoice", ":id") }}'.replace(':id', row.conversation_id);
                                actions += `<a href="${invoiceUrl}" class="btn btn-sm btn-quick-action btn-warning" title="View Invoice" target="_blank">
                                    <i class="bx bx-file"></i> <span class="d-none d-md-inline">Invoice</span>
                                </a>`;
                            }
                            
                            if (row.has_conversation || row.has_booking) {
                                actions += `<span class="badge bg-info">Done</span>`;
                            }
                            
                            actions += '</div>';
                            return actions;
                        }
                    }
                ],
                order: [[1, 'asc']],
                pageLength: 25,
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                }
            });

            // Initialize Select2 with AJAX for table selection (handles 2000+ tables) - Make it global
            window.initTableSelect2 = function(selectId, locationId) {
                $(selectId).select2('destroy');
                $(selectId).empty().append('<option value="">-- Select Table --</option>');
                
                if (!locationId) {
                    let dropdownParent = $('body');
                    if (selectId === '#qb_table_id') {
                        dropdownParent = $('#quickBookingModal');
                    } else if (selectId === '#qc_table_id') {
                        dropdownParent = $('#quickConversationModal');
                    } else if (selectId === '#canvas_table_id') {
                        dropdownParent = $('#canvasConversationModal');
                    }
                    $(selectId).select2({
                        theme: 'bootstrap-5', 
                        width: '100%',
                        dropdownParent: dropdownParent
                    });
                    return;
                }
                
                let dropdownParent = $('body');
                if (selectId === '#qb_table_id') {
                    dropdownParent = $('#quickBookingModal');
                } else if (selectId === '#qc_table_id') {
                    dropdownParent = $('#quickConversationModal');
                } else if (selectId === '#canvas_table_id') {
                    dropdownParent = $('#canvasConversationModal');
                }
                
                $(selectId).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '-- Search and Select Table --',
                    allowClear: true,
                    dropdownParent: dropdownParent || $('body'),
                    ajax: {
                        url: `{{ route('booking.getTables', ':id') }}`.replace(':id', locationId),
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            return {
                                search: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            
                            let results = [];
                            if (data.results) {
                                results = data.results;
                            } else if (Array.isArray(data)) {
                                results = data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.table_no + ' - ₹' + parseFloat(item.price || 0).toFixed(2) + (item.table_size ? ' (' + item.table_size + ')' : ''),
                                        table_no: item.table_no,
                                        price: item.price || 0,
                                        table_size: item.table_size || null
                                    };
                                });
                            }
                            
                            return {
                                results: results,
                                pagination: {
                                    more: data.pagination ? data.pagination.more : false
                                }
                            };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });
        };

        // Load tables when location changes in conversation modal
        $(document).on('change', '#qc_location_id', function() {
            const locationId = $(this).val();
            initTableSelect2('#qc_table_id', locationId);
        });
        
        // Load tables when location changes in booking modal
        $(document).on('change', '#qb_location_id', function() {
            const locationId = $(this).val();
            initTableSelect2('#qb_table_id', locationId);
        });
        
        // Set price when table is selected in booking form
        $('#qb_table_id').on('change', function() {
            const tableId = $(this).val();
            if (tableId) {
                const tableData = $(this).select2('data')[0];
                if (tableData && tableData.price) {
                    $('#qb_price').val(tableData.price);
                } else {
                    // Fallback: fetch price via API
                    $.ajax({
                        url: `{{ route('booking.getPrice', ':id') }}`.replace(':id', tableId),
                        type: 'GET',
                        success: function(response) {
                            $('#qb_price').val(response.price || 0);
                        },
                        error: function() {
                            console.error('Failed to fetch table price');
                        }
                    });
                }
            } else {
                $('#qb_price').val('');
            }
        });
        
        // Initialize Select2 for conversation modal when shown
        $('#quickConversationModal').on('shown.bs.modal', function() {
            // Initialize location select if needed
            if (!$('#qc_location_id').hasClass('select2-hidden-accessible')) {
                $('#qc_location_id').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#quickConversationModal')
                });
            }
            
            // Initialize table select if needed (empty state)
            if (!$('#qc_table_id').hasClass('select2-hidden-accessible')) {
                $('#qc_table_id').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#quickConversationModal'),
                    placeholder: '-- Select Location First --'
                });
            }
        });
        
        // Initialize Select2 for booking modal when shown
        $('#quickBookingModal').on('shown.bs.modal', function() {
            // Initialize location select if needed
            if (!$('#qb_location_id').hasClass('select2-hidden-accessible')) {
                $('#qb_location_id').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#quickBookingModal')
                });
            }
            
            // Initialize table select if needed (empty state)
            if (!$('#qb_table_id').hasClass('select2-hidden-accessible')) {
                $('#qb_table_id').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#quickBookingModal'),
                    placeholder: '-- Select Location First --'
                });
            }
        });
        });

        // Global function to open conversations canvas
        window.openConversationsCanvas = function(contactId, contactName, contactPhone, contactEmail) {
            try {
                console.log('Opening conversations canvas for:', contactId, contactName);
                
                // Check if canvas element exists
                const canvasElement = document.getElementById('conversationsCanvas');
                if (!canvasElement) {
                    console.error('Conversations canvas element not found!');
                    toastr.error('Conversations panel not found. Please refresh the page.');
                    return;
                }
                
                // Set contact info in canvas header
                $('#canvasContactName').html(`<i class="bx bx-conversation me-2"></i>Conversations - ${contactName || 'Unknown'}`);
                $('#canvasContactInfo').html(`${contactPhone || ''} ${contactEmail ? ' • ' + contactEmail : ''}`);
                
                // Store contact ID for later use
                $('#conversationsCanvas').data('contact-id', contactId);
                $('#conversationsCanvas').data('contact-name', contactName);
                $('#conversationsCanvas').data('contact-phone', contactPhone);
                $('#conversationsCanvas').data('contact-email', contactEmail);
                
                // Show loading state
                $('#conversationsLoading').show();
                $('#conversationsList').hide();
                $('#conversationsEmpty').hide();
                
                // Open canvas
                const canvas = new bootstrap.Offcanvas(canvasElement);
                canvas.show();
                
                // Load conversations after a small delay to ensure canvas is visible
                setTimeout(function() {
                    loadConversationsForVisitor(contactId);
                }, 300);
            } catch (error) {
                console.error('Error opening conversations canvas:', error);
                toastr.error('Error opening conversations panel. Please try again.');
            }
        };

        // Load conversations for a visitor
        function loadConversationsForVisitor(contactId) {
            console.log('Loading conversations for contact:', contactId);
            
            $.ajax({
                url: '{{ route("campaigns.conversations.visitor", $campaign->id) }}',
                type: 'POST',
                data: {
                    visitor_id: contactId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Conversations response:', response);
                    $('#conversationsLoading').hide();
                    
                    if (response.status && response.conversations && response.conversations.length > 0) {
                        displayConversations(response.conversations);
                        $('#conversationsList').show();
                        $('#conversationsEmpty').hide();
                    } else {
                        $('#conversationsList').hide();
                        $('#conversationsEmpty').show();
                        console.log('No conversations found for this contact');
                    }
                },
                error: function(xhr) {
                    $('#conversationsLoading').hide();
                    $('#conversationsList').hide();
                    $('#conversationsEmpty').show();
                    console.error('Error loading conversations:', xhr);
                    const errorMessage = xhr.responseJSON?.message || xhr.statusText || 'Failed to load conversations';
                    toastr.error('Failed to load conversations: ' + errorMessage);
                }
            });
        }

        // Display conversations in the canvas
        function displayConversations(conversations) {
            let html = '<div class="p-2">';
            
            conversations.forEach(function(conv, index) {
                const badgeClass = conv.outcome === 'materialised' ? 'bg-success' : 
                                  (conv.outcome === 'interested' ? 'bg-info' : 'bg-warning');
                
                html += `
                    <div class="card mb-2 shadow-sm border-0" style="border-left: 3px solid var(--sidebar-end, #3b82f6) !important; overflow: hidden;">
                        <div class="card-header p-2" style="background: linear-gradient(135deg, var(--sidebar-start, #1e3a8a) 0%, var(--sidebar-end, #3b82f6) 100%); color: white; border: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bx bx-building me-1" style="font-size: 0.9rem;"></i>
                                        <span class="fw-semibold" style="font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${conv.exhibitor_name || 'N/A'}</span>
                                    </div>
                                    <div class="d-flex align-items-center" style="font-size: 0.75rem; opacity: 0.9;">
                                        <i class="bx bx-user me-1"></i>
                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${conv.employee_name || 'N/A'}</span>
                                    </div>
                                </div>
                                <span class="badge ${badgeClass} ms-2" style="font-size: 0.7rem; padding: 4px 8px; flex-shrink: 0;">${conv.outcome ? conv.outcome.toUpperCase() : 'N/A'}</span>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            ${conv.location_name !== 'N/A' ? `
                            <div class="mb-1 d-flex align-items-center" style="font-size: 0.75rem; color: #6b7280;">
                                <i class="bx bx-map me-1"></i>
                                <span>${conv.location_name}${conv.table_no !== 'N/A' ? ` • Table: ${conv.table_no}` : ''}</span>
                            </div>
                            ` : ''}
                            
                            ${conv.notes ? `
                            <div class="mb-1 p-1 bg-light rounded" style="font-size: 0.75rem; line-height: 1.4;">
                                ${conv.notes.length > 100 ? conv.notes.substring(0, 100) + '...' : conv.notes}
                            </div>
                            ` : ''}
                            
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div style="font-size: 0.7rem; color: #9ca3af;">
                                    <i class="bx bx-time me-1"></i>${conv.conversation_date}
                                </div>
                                ${conv.has_booking && conv.price ? `
                                <div style="font-size: 0.7rem; color: #059669; font-weight: 600;">
                                    <i class="bx bx-calendar-check me-1"></i>₹${conv.price}${conv.amount_paid ? ` (₹${conv.amount_paid})` : ''}
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            $('#conversationsList').html(html);
        }

        // Handle canvas add conversation button
        $('#canvasAddConversationBtn').on('click', function() {
            const contactId = $('#conversationsCanvas').data('contact-id');
            const contactName = $('#conversationsCanvas').data('contact-name');
            
            if (!contactId) {
                toastr.error('Contact information not found');
                return;
            }
            
            // Reset form completely
            const form = $('#canvasConversationForm')[0];
            if (form) {
                form.reset();
            }
            
            // Set hidden and fixed values
            $('#canvas_campaign_id').val('{{ $campaign->id }}');
            $('#canvas_visitor_id').val(contactId);
            $('#canvas_visitor_name').val(contactName || '');
            $('#canvasConversationForm input[name="employee_id"]').val('{{ auth()->id() }}');
            $('#canvas_exhibitor_id').val('');
            
            // Reset location select
            if ($('#canvas_location_id').hasClass('select2-hidden-accessible')) {
                $('#canvas_location_id').val(null).trigger('change');
            } else {
                $('#canvas_location_id').val('');
            }
            
            // Reset table select
            if ($('#canvas_table_id').hasClass('select2-hidden-accessible')) {
                $('#canvas_table_id').select2('destroy');
            }
            $('#canvas_table_id').empty().append('<option value="">-- Select Table --</option>');
            
            // Reset other fields
            $('#canvas_outcome').val('');
            $('#canvas_notes').val('');
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('canvasConversationModal'));
            modal.show();
        });

        // Initialize Select2 for canvas conversation modal
        $('#canvasConversationModal').on('shown.bs.modal', function() {
            // Initialize location select with Select2
            if ($('#canvas_location_id').hasClass('select2-hidden-accessible')) {
                $('#canvas_location_id').select2('destroy');
            }
            $('#canvas_location_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $('#canvasConversationModal'),
                placeholder: '-- Select Location --',
                allowClear: true
            });
            
            // Initialize table select (empty state)
            if ($('#canvas_table_id').hasClass('select2-hidden-accessible')) {
                $('#canvas_table_id').select2('destroy');
            }
            $('#canvas_table_id').empty().append('<option value="">-- Select Table --</option>');
            $('#canvas_table_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $('#canvasConversationModal'),
                placeholder: '-- Select Location First --',
                allowClear: true
            });
        });
        
        // Clean up Select2 when modal is hidden
        $('#canvasConversationModal').on('hidden.bs.modal', function() {
            // Don't destroy, just reset values
            if ($('#canvas_location_id').hasClass('select2-hidden-accessible')) {
                $('#canvas_location_id').val(null).trigger('change');
            }
            if ($('#canvas_table_id').hasClass('select2-hidden-accessible')) {
                $('#canvas_table_id').val(null).trigger('change');
            }
        });

        // Load tables when location changes in canvas conversation form
        $(document).on('change', '#canvas_location_id', function() {
            const locationId = $(this).val();
            console.log('Location changed:', locationId); // Debug
            
            if (locationId) {
                // Load tables for this location
                initTableSelect2('#canvas_table_id', locationId);
            } else {
                // Reset table select
                if ($('#canvas_table_id').hasClass('select2-hidden-accessible')) {
                    $('#canvas_table_id').select2('destroy');
                }
                $('#canvas_table_id').empty().append('<option value="">-- Select Table --</option>');
                $('#canvas_table_id').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#canvasConversationModal'),
                    placeholder: '-- Select Location First --',
                    allowClear: true
                });
            }
        });

        // Handle canvas conversation form submission
        $('#canvasConversationForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '{{ route("campaigns.conversations.store", $campaign->id) }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        
                        // Reset form for next conversation
                        const form = $('#canvasConversationForm')[0];
                        if (form) {
                            form.reset();
                        }
                        
                        // Set fixed values again
                        const contactId = $('#conversationsCanvas').data('contact-id');
                        const contactName = $('#conversationsCanvas').data('contact-name');
                        $('#canvas_campaign_id').val('{{ $campaign->id }}');
                        $('#canvas_visitor_id').val(contactId);
                        $('#canvas_visitor_name').val(contactName || '');
                        $('#canvasConversationForm input[name="employee_id"]').val('{{ auth()->id() }}');
                        $('#canvas_exhibitor_id').val('');
                        
                        // Reset location and table selects
                        if ($('#canvas_location_id').hasClass('select2-hidden-accessible')) {
                            $('#canvas_location_id').val(null).trigger('change');
                        } else {
                            $('#canvas_location_id').val('');
                        }
                        
                        if ($('#canvas_table_id').hasClass('select2-hidden-accessible')) {
                            $('#canvas_table_id').select2('destroy');
                        }
                        $('#canvas_table_id').empty().append('<option value="">-- Select Table --</option>');
                        $('#canvas_table_id').select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            dropdownParent: $('#canvasConversationModal'),
                            placeholder: '-- Select Location First --',
                            allowClear: true
                        });
                        
                        // Reset other fields
                        $('#canvas_outcome').val('');
                        $('#canvas_notes').val('');
                        
                        // Don't close modal - allow adding more conversations
                        // bootstrap.Modal.getInstance(document.getElementById('canvasConversationModal')).hide();
                        
                        // Reload conversations in canvas
                        if (contactId) {
                            loadConversationsForVisitor(contactId);
                        }
                        
                        // Reload recipients table
                        $('#recipientsTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors || {};
                    let errorMsg = 'Failed to create conversation';
                    if (Object.keys(errors).length > 0) {
                        errorMsg = Object.values(errors).flat().join(', ');
                    }
                    toastr.error(errorMsg);
                }
            });
        });

        // Global functions for opening modals (defined outside document.ready)
        window.openQuickConversation = function(visitorId, visitorName) {
            try {
                // Reset form
                const form = $('#quickConversationForm')[0];
                if (form) {
                    form.reset();
                }
                
                $('#qc_campaign_id').val('{{ $campaign->id }}');
                $('#qc_visitor_id').val(visitorId || '');
                $('#qc_visitor_name').val(visitorName || '');
                $('#quickConversationForm input[name="employee_id"]').val('{{ auth()->id() }}');
                
                // Reset selects
                $('#qc_exhibitor_id').val('');
                
                if ($('#qc_location_id').hasClass('select2-hidden-accessible')) {
                    $('#qc_location_id').val(null).trigger('change');
                } else {
                    $('#qc_location_id').val('');
                }
                
                if ($('#qc_table_id').hasClass('select2-hidden-accessible')) {
                    $('#qc_table_id').val(null).trigger('change');
                } else {
                    $('#qc_table_id').empty().append('<option value="">-- Select Table --</option>');
                }
                
                $('#qc_outcome').val('');
                $('#qc_notes').val('');
                
                // Show modal
                const modalElement = document.getElementById('quickConversationModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    console.error('Modal element not found');
                    toastr.error('Error: Conversation modal not found. Please refresh the page.');
                }
            } catch (error) {
                console.error('Error opening conversation modal:', error);
                toastr.error('Error opening conversation form. Please try again.');
            }
        };

        window.openQuickBooking = function(contactId, contactName) {
            try {
                // Reset form but preserve hidden fields structure
                const form = $('#quickBookingForm')[0];
                if (form) {
                    form.reset();
                }
                
                // Use the selected contact as exhibitor (not visitor)
                $('#qb_campaign_id').val('{{ $campaign->id }}');
                $('#qb_exhibitor_id').val(contactId || '');
                $('#qb_exhibitor_name').val(contactName || '');
                $('#qb_visitor_id').val(''); // Clear visitor_id for exhibitor bookings
                $('#quickBookingForm input[name="employee_id"]').val('{{ auth()->id() }}');
                
                // Reset selects and price
                if ($('#qb_location_id').hasClass('select2-hidden-accessible')) {
                    $('#qb_location_id').val(null).trigger('change');
                } else {
                    $('#qb_location_id').val('');
                }
                
                // Reset table select
                if ($('#qb_table_id').hasClass('select2-hidden-accessible')) {
                    $('#qb_table_id').val(null).trigger('change');
                } else {
                    $('#qb_table_id').empty().append('<option value="">-- Select Table --</option>');
                }
                
                $('#qb_price').val('');
                $('#quickBookingForm input[name="booking_date"]').val('{{ date("Y-m-d") }}');
                $('#quickBookingForm input[name="amount_paid"]').val('0');
                $('#quickBookingForm select[name="amount_status"]').val('pending');
                
                // Show modal
                const modalElement = document.getElementById('quickBookingModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    console.error('Modal element not found');
                    toastr.error('Error: Booking modal not found. Please refresh the page.');
                }
            } catch (error) {
                console.error('Error opening booking modal:', error);
                toastr.error('Error opening booking form. Please try again.');
            }
        };

        // Handle quick conversation form
        $('#quickConversationForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '{{ route("campaigns.conversations.store", $campaign->id) }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        bootstrap.Modal.getInstance(document.getElementById('quickConversationModal')).hide();
                        $('#quickConversationForm')[0].reset();
                        $('#recipientsTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors || {};
                    let errorMsg = 'Failed to create conversation';
                    if (Object.keys(errors).length > 0) {
                        errorMsg = Object.values(errors).flat().join(', ');
                    }
                    toastr.error(errorMsg);
                }
            });
        });

        // Handle quick booking form
        $('#quickBookingForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '{{ route("table-availability.booking") }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        bootstrap.Modal.getInstance(document.getElementById('quickBookingModal')).hide();
                        $('#quickBookingForm')[0].reset();
                        $('#recipientsTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors || {};
                    let errorMsg = 'Failed to create booking';
                    if (Object.keys(errors).length > 0) {
                        errorMsg = Object.values(errors).flat().join(', ');
                    }
                    toastr.error(errorMsg);
                }
            });
        });
    </script>
@endsection

