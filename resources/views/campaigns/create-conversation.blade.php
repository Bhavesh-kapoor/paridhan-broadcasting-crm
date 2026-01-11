@extends('layouts.app_layout')

@section('title', 'Add Conversation')

@section('style')
    <style>
        :root {
            --sidebar-start: #1e3a8a;
            --sidebar-end: #3b82f6;
        }
        
        .section-card {
            border-radius: 12px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        
        .section-card:hover {
            border-left-color: var(--sidebar-end);
        }
        
        .booking-toggle {
            cursor: pointer;
        }
        
        .booking-section {
            border: 2px dashed var(--sidebar-end);
            border-radius: 12px;
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%);
            border: none;
            color: white;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.2);
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, var(--sidebar-end) 0%, var(--sidebar-start) 100%);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.4);
            color: white;
        }
        
        .table-info-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 12px;
            margin-top: 10px;
        }
    </style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Add Conversation</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            @if(auth()->user()->role == 'admin')
                            <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
                            @else
                            <li class="breadcrumb-item"><a href="{{ route('employee.campaigns.index') }}">Campaigns</a></li>
                            @endif
                            <li class="breadcrumb-item"><a href="{{ route('campaigns.conversations', $campaign->id) }}">Conversations</a></li>
                            <li class="breadcrumb-item active">Add Conversation</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Campaign Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); border-radius: 12px;">
                        <div class="card-body p-4">
                            <h4 class="text-white mb-1">
                                <i class="bx bx-conversation me-2"></i>Add Conversation for: {{ $campaign->name }}
                            </h4>
                            <p class="text-white-50 mb-0">{{ $campaign->subject }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <form id="conversationForm" method="POST" action="{{ route('campaigns.conversations.store', $campaign->id) }}">
                @csrf
                
                <div class="row">
                    <!-- Company & Visitor Information -->
                    <div class="col-lg-6 mb-4">
                        <div class="card section-card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="mb-4 fw-bold d-flex align-items-center">
                                    <i class="bx bx-building me-2" style="color: var(--sidebar-end);"></i>Company & Visitor Information
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-store-alt me-1"></i>Exhibitor (Company) <span class="text-danger">*</span>
                                    </label>
                                    <select name="exhibitor_id" id="exhibitor_id" class="form-select select2" required>
                                        <option value="">-- Select Exhibitor --</option>
                                        @foreach($exhibitors as $exhibitor)
                                            <option value="{{ $exhibitor->id }}">{{ $exhibitor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-user me-1"></i>Visitor/Lead
                                    </label>
                                    <select name="visitor_id" id="visitor_id" class="form-select select2">
                                        <option value="">-- Select Visitor --</option>
                                        @foreach($visitors as $visitor)
                                            <option value="{{ $visitor->id }}">{{ $visitor->name }} ({{ $visitor->phone }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select visitor from campaign recipients if available</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-phone me-1"></i>Visitor Phone (if not in contacts)
                                    </label>
                                    <input type="text" class="form-control" name="visitor_phone" id="visitor_phone" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employee & Location Information -->
                    <div class="col-lg-6 mb-4">
                        <div class="card section-card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="mb-4 fw-bold d-flex align-items-center">
                                    <i class="bx bx-map-pin me-2" style="color: var(--sidebar-end);"></i>Location & Table Information
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-user-check me-1"></i>Handling Employee <span class="text-danger">*</span>
                                    </label>
                                    <select name="employee_id" id="employee_id" class="form-select select2" required>
                                        <option value="">-- Select Employee --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ auth()->id() == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-map me-1"></i>Location
                                    </label>
                                    <select name="location_id" id="location_id" class="form-select select2">
                                        <option value="">-- Select Location --</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->loc_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-table me-1"></i>Stall/Table <small class="text-muted">(Searchable - handles 2000+ tables)</small>
                                    </label>
                                    <select name="table_id" id="table_id" class="form-select select2-table" data-placeholder="-- Search and Select Table --">
                                        <option value="">-- Select Table --</option>
                                    </select>
                                    <small class="text-muted">Type to search tables. Tables are loaded dynamically.</small>
                                    <div id="tableInfo" class="table-info-card" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong id="tableNoDisplay">-</strong>
                                                <small class="d-block text-muted" id="tableSizeDisplay"></small>
                                            </div>
                                            <div class="text-end">
                                                <strong class="text-success" id="tablePriceDisplay">₹0.00</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conversation Details -->
                    <div class="col-lg-6 mb-4">
                        <div class="card section-card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="mb-4 fw-bold d-flex align-items-center">
                                    <i class="bx bx-conversation me-2" style="color: var(--sidebar-end);"></i>Conversation Details
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-info-circle me-1"></i>Outcome <span class="text-danger">*</span>
                                    </label>
                                    <select name="outcome" id="outcome" class="form-select" required>
                                        <option value="">-- Select Outcome --</option>
                                        <option value="busy">Busy</option>
                                        <option value="interested">Interested</option>
                                        <option value="materialised">Materialised</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-calendar me-1"></i>Conversation Date
                                    </label>
                                    <input type="datetime-local" class="form-control" name="conversation_date" id="conversation_date" value="{{ now()->format('Y-m-d\TH:i') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-note me-1"></i>Notes
                                    </label>
                                    <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="Enter conversation notes/comments..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Section (Toggleable) -->
                    <div class="col-lg-6 mb-4">
                        <div class="card section-card border-0 shadow-sm" id="bookingCard">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 fw-bold d-flex align-items-center">
                                        <i class="bx bx-calendar-check me-2" style="color: var(--sidebar-end);"></i>Table Booking
                                    </h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input booking-toggle" type="checkbox" id="enableBooking" name="enable_booking">
                                        <label class="form-check-label" for="enableBooking">
                                            <strong>Book Table</strong>
                                        </label>
                                    </div>
                                </div>
                                
                                <div id="bookingSection" style="display: none;">
                                    <div class="booking-section p-3">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="bx bx-calendar me-1"></i>Booking Date <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control" name="booking_date" id="booking_date" value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="bx bx-rupee me-1"></i>Total Price <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" class="form-control" name="booking_price" id="booking_price" placeholder="0.00" readonly>
                                                <small class="text-muted">Auto-filled from selected table</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="bx bx-wallet me-1"></i>Amount Paid
                                                </label>
                                                <input type="number" step="0.01" class="form-control" name="booking_amount_paid" id="booking_amount_paid" placeholder="0.00" value="0">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="bx bx-check-circle me-1"></i>Payment Status <span class="text-danger">*</span>
                                                </label>
                                                <select name="booking_amount_status" id="booking_amount_status" class="form-select">
                                                    <option value="pending">Pending</option>
                                                    <option value="partial">Partial</option>
                                                    <option value="paid">Paid</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="alert alert-info mb-0 mt-3">
                                            <i class="bx bx-info-circle me-2"></i>
                                            <small>Booking will be created along with the conversation. The conversation outcome will be automatically set to "materialised" when booking is enabled.</small>
                                        </div>
                                    </div>
                                </div>
                                <div id="bookingDisabled" class="text-center py-3 text-muted">
                                    <i class="bx bx-calendar-check fs-4 mb-2 d-block"></i>
                                    <small>Enable booking to create a table booking along with the conversation</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('campaigns.conversations', $campaign->id) }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-arrow-back me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-gradient btn-lg px-5">
                                        <i class="bx bx-check-circle me-1"></i>Add Conversation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for regular selects
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Initialize Select2 for table with AJAX (handles 2000+ tables efficiently)
            $('#table_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Search and Select Table --',
                allowClear: true,
                ajax: {
                    url: function() {
                        const locationId = $('#location_id').val();
                        if (!locationId) {
                            return null;
                        }
                        return `{{ route('booking.getTables', ':id') }}`.replace(':id', locationId);
                    },
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
                        
                        // Handle both old format (array) and new format (Select2 format)
                        let results = [];
                        if (data.results) {
                            // Select2 format
                            results = data.results;
                        } else if (Array.isArray(data)) {
                            // Legacy format
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

            // Load tables when location changes (trigger Select2 to load)
            $('#location_id').on('change', function() {
                const locationId = $(this).val();
                const tableSelect = $('#table_id');
                
                if (locationId) {
                    // Clear and reinitialize Select2 with new URL
                    tableSelect.empty().append('<option value="">-- Select Table --</option>').trigger('change');
                    tableSelect.select2('destroy');
                    tableSelect.select2({
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
                } else {
                    tableSelect.empty().append('<option value="">-- Select Table --</option>').trigger('change');
                    $('#tableInfo').hide();
                }
            });

            // Show table info when table is selected
            $('#table_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const tableId = $(this).val();
                
                if (tableId) {
                    // Fetch table details
                    $.ajax({
                        url: `{{ route('booking.getPrice', ':id') }}`.replace(':id', tableId),
                        type: 'GET',
                        success: function(response) {
                            // Get table info from Select2 data
                            const tableData = $('#table_id').select2('data')[0];
                            if (tableData) {
                                $('#tableNoDisplay').text(tableData.text.split(' - ')[0]);
                                $('#tablePriceDisplay').text('₹' + parseFloat(response.price || tableData.price || 0).toFixed(2));
                                if (tableData.table_size) {
                                    $('#tableSizeDisplay').text('Size: ' + tableData.table_size);
                                } else {
                                    $('#tableSizeDisplay').text('');
                                }
                                $('#tableInfo').fadeIn();
                                
                                // Auto-fill booking price if booking is enabled
                                if ($('#enableBooking').is(':checked')) {
                                    $('#booking_price').val(response.price || tableData.price || 0);
                                }
                            }
                        }
                    });
                } else {
                    $('#tableInfo').hide();
                }
            });

            // Booking toggle
            $('#enableBooking').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#bookingSection').slideDown();
                    $('#bookingDisabled').hide();
                    
                    // Auto-fill price if table is selected
                    const tableId = $('#table_id').val();
                    if (tableId) {
                        $.ajax({
                            url: `{{ route('booking.getPrice', ':id') }}`.replace(':id', tableId),
                            type: 'GET',
                            success: function(response) {
                                $('#booking_price').val(response.price || 0);
                            }
                        });
                    }
                    
                    // Set outcome to materialised
                    $('#outcome').val('materialised').trigger('change');
                } else {
                    $('#bookingSection').slideUp();
                    $('#bookingDisabled').show();
                }
            });

            // Form submission
            $('#conversationForm').on('submit', function(e) {
                e.preventDefault();
                
                // Add enable_booking checkbox value if checked
                const formData = $(this).serialize();
                let submitData = formData;
                
                if ($('#enableBooking').is(':checked')) {
                    submitData += '&enable_booking=on';
                }
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: submitData,
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            window.location.href = '{{ route('campaigns.conversations', $campaign->id) }}';
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMsg = 'Validation errors:\n';
                            Object.keys(errors).forEach(key => {
                                errorMsg += `- ${errors[key][0]}\n`;
                            });
                            toastr.error(errorMsg);
                        } else {
                            toastr.error('Failed to create conversation');
                        }
                    }
                });
            });
        });
    </script>
@endsection
