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
        
        /* Responsive table styles */
        @media (max-width: 768px) {
            #recipientsTable {
                font-size: 0.85rem;
            }
            
            #recipientsTable thead th {
                padding: 8px 6px !important;
                font-size: 0.8rem;
            }
            
            #recipientsTable tbody td {
                padding: 8px 6px !important;
                font-size: 0.8rem;
            }
            
            .btn-quick-action {
                padding: 0.3rem 0.5rem;
                font-size: 0.75rem;
            }
            
            .btn-quick-action i {
                font-size: 0.9rem;
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
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="recipientsTable">
                                    <thead class="table-header-gradient">
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
                            <label class="form-label fw-semibold">Visitor Name</label>
                            <input type="text" class="form-control" id="qc_visitor_name" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Exhibitor (Company) <span class="text-danger">*</span></label>
                            <select name="exhibitor_id" id="qc_exhibitor_id" class="form-select" required>
                                <option value="">-- Select Exhibitor --</option>
                                @foreach($exhibitors as $exhibitor)
                                    <option value="{{ $exhibitor->id }}">{{ $exhibitor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
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
                            <label class="form-label fw-semibold">Visitor Name</label>
                            <input type="text" class="form-control" id="qb_visitor_name" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Exhibitor (Company) <span class="text-danger">*</span></label>
                            <select name="exhibitor_id" id="qb_exhibitor_id" class="form-select" required>
                                <option value="">-- Select Exhibitor --</option>
                                @foreach($exhibitors as $exhibitor)
                                    <option value="{{ $exhibitor->id }}">{{ $exhibitor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
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
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var recipientsTable = $('#recipientsTable').DataTable({
                processing: true,
                serverSide: false,
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
                        render: function(data) {
                            return data ? '<span class="badge bg-success"><i class="bx bx-check"></i> Yes</span>' : '<span class="badge bg-secondary">No</span>';
                        }
                    },
                    { 
                        data: 'id', 
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            let actions = '<div class="d-flex gap-1 justify-content-center flex-wrap">';
                            
                            if (!row.has_conversation) {
                                actions += `<button class="btn btn-sm btn-quick-action btn-primary" onclick="openQuickConversation('${row.contact_id}', '${row.name.replace(/'/g, "\\'")}')" title="Add Conversation">
                                    <i class="bx bx-conversation"></i> <span class="d-none d-md-inline">Conversation</span>
                                </button>`;
                            }
                            
                            if (!row.has_booking) {
                                actions += `<button class="btn btn-sm btn-quick-action btn-success" onclick="openQuickBooking('${row.contact_id}', '${row.name.replace(/'/g, "\\'")}')" title="Book Table">
                                    <i class="bx bx-calendar-check"></i> <span class="d-none d-md-inline">Book</span>
                                </button>`;
                            }
                            
                            // Always show view button if conversation exists
                            if (row.has_conversation && row.conversation_id) {
                                actions += `<a href="{{ route('campaigns.conversations', $campaign->id) }}" class="btn btn-sm btn-quick-action btn-info" title="View Conversations">
                                    <i class="bx bx-show"></i> <span class="d-none d-md-inline">View</span>
                                </a>`;
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

            // Initialize Select2 with AJAX for table selection (handles 2000+ tables)
            function initTableSelect2(selectId, locationId) {
                $(selectId).select2('destroy');
                $(selectId).empty().append('<option value="">-- Select Table --</option>');
                
                if (!locationId) {
                    $(selectId).select2({theme: 'bootstrap-5', width: '100%'});
                    return;
                }
                
                $(selectId).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '-- Search and Select Table --',
                    allowClear: true,
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
            }

        // Load tables when location changes
        $('#qc_location_id, #qb_location_id').on('change', function() {
            const locationId = $(this).val();
            const isBooking = $(this).attr('id') === 'qb_location_id';
            const tableSelect = isBooking ? '#qb_table_id' : '#qc_table_id';
            
            initTableSelect2(tableSelect, locationId);
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
                        }
                    });
                }
            }
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
                            }
                        });
                    }
                }
            });
        });

        function openQuickConversation(visitorId, visitorName) {
            $('#qc_visitor_id').val(visitorId);
            $('#qc_visitor_name').val(visitorName);
            new bootstrap.Modal(document.getElementById('quickConversationModal')).show();
        }

        function openQuickBooking(visitorId, visitorName) {
            $('#qb_visitor_id').val(visitorId);
            $('#qb_visitor_name').val(visitorName);
            new bootstrap.Modal(document.getElementById('quickBookingModal')).show();
        }

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

