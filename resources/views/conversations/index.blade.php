@extends('layouts.app_layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('/assets/css/enhanced-tables.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --sidebar-start: #ec268f;
            --sidebar-end: #f06292;
        }
    </style>
@endsection
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Conversations</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Conversations</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            
            <!--table wrapper -->
            <div class="card mb-0 border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bx bx-conversation me-2 text-primary"></i>Conversation Management
                        </h6>
                        <button type="button" class="btn btn-primary" id="btnAddConversation">
                            <i class="bx bx-plus me-1"></i>Add New Conversation
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table id="conversations_table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr class="table-header-gradient">
                                    <th class="text-center"><i class="bx bx-hash me-1"></i>Sl.No</th>
                                    <th><i class="bx bx-building me-1"></i>Exhibitor</th>
                                    <th><i class="bx bx-user me-1"></i>Visitor</th>
                                    <th><i class="bx bx-user-circle me-1"></i>Employee</th>
                                    <th><i class="bx bx-map me-1"></i>Location</th>
                                    <th><i class="bx bx-table me-1"></i>Table</th>
                                    <th><i class="bx bx-megaphone me-1"></i>Campaign</th>
                                    <th class="text-center"><i class="bx bx-info-circle me-1"></i>Outcome</th>
                                    <th><i class="bx bx-calendar me-1"></i>Date</th>
                                    <th class="text-center"><i class="bx bx-cog me-1"></i>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!--end table wrapper-->
        </div>
    </div>

    <!-- Conversation Modal -->
    <div class="modal fade" id="conversationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="bx bx-conversation me-2"></i>Add Conversation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="conversationForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="conversation_id" id="conversation_id">
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Exhibitor Name</label>
                                <input type="text" class="form-control" name="exhibitor_name" id="exhibitor_name" placeholder="Enter exhibitor/company name">
                                <input type="hidden" name="exhibitor_id" id="exhibitor_id">
                                <small class="text-muted">Type the exhibitor name (will be created if not exists)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Visitor</label>
                                <select name="visitor_id" id="visitor_id" class="form-select select2">
                                    <option value="">-- Select Visitor --</option>
                                    @foreach($visitors as $visitor)
                                        <option value="{{ $visitor->id }}">{{ $visitor->name }} ({{ $visitor->phone ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Visitor Phone</label>
                                <input type="text" class="form-control" name="visitor_phone" id="visitor_phone" placeholder="Enter phone if visitor not selected">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Campaign</label>
                                <select name="campaign_id" id="campaign_id" class="form-select select2">
                                    <option value="">-- Select Campaign --</option>
                                    @foreach($campaigns as $campaign)
                                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id" class="form-select select2" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ auth()->id() == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Location</label>
                                <select name="location_id" id="location_id" class="form-select select2">
                                    <option value="">-- Select Location --</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->loc_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Table</label>
                                <select name="table_id" id="table_id" class="form-select select2">
                                    <option value="">-- Select Table --</option>
                                </select>
                                <small class="text-muted">Select location first to load tables</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Outcome <span class="text-danger">*</span></label>
                                <select name="outcome" id="outcome" class="form-select" required>
                                    <option value="">-- Select Outcome --</option>
                                    <option value="busy">Busy</option>
                                    <option value="interested">Interested</option>
                                    <option value="materialised">Materialised</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Conversation Date</label>
                                <input type="datetime-local" class="form-control" name="conversation_date" id="conversation_date" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="Enter conversation notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="bx bx-check me-1"></i>Save Conversation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Side Conversations Canvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="conversationsCanvas" style="width: 600px;">
        <div class="offcanvas-header border-bottom" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); color: white;">
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
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const conversationsTable = $('#conversations_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollX: true,
                scrollCollapse: true,
                pagination: true,
                ajax: {
                    url: `{{ auth()->user()->role === 'admin' ? route('conversations.list') : route('employee.conversations.list') }}`,
                    type: 'POST',
                    data: function(d) {
                        d.status = '{{ $status ?? '' }}';
                    }
                },
                columns: [{
                        data: null,
                        name: 'id',
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                    },
                    { data: 'exhibitor_name', name: 'exhibitor_name' },
                    { data: 'visitor_name', name: 'visitor_name' },
                    { data: 'employee_name', name: 'employee_name' },
                    { data: 'location_name', name: 'location_name' },
                    { data: 'table_name', name: 'table_name' },
                    { data: 'campaign_name', name: 'campaign_name' },
                    { data: 'outcome_badge', name: 'outcome', className: "text-center" },
                    { data: 'conversation_date', name: 'conversation_date' },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                ],
                "columnDefs": [{
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    "orderable": false,
                    "sorting": false
                }],
            });

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $('#conversationModal')
            });

            // Load tables when location changes
            $('#location_id').on('change', function() {
                const locationId = $(this).val();
                const tableSelect = $('#table_id');
                
                if (locationId) {
                    tableSelect.html('<option value="">Loading...</option>');
                    $.ajax({
                        url: `{{ auth()->user()->role === 'admin' ? route('conversations.getTables', ':id') : route('employee.conversations.getTables', ':id') }}`.replace(':id', locationId),
                        type: 'GET',
                        success: function(response) {
                            tableSelect.html('<option value="">-- Select Table --</option>');
                            response.forEach(function(table) {
                                tableSelect.append(`<option value="${table.id}">${table.table_no}</option>`);
                            });
                        },
                        error: function() {
                            tableSelect.html('<option value="">Error loading tables</option>');
                            toastr.error('Failed to load tables');
                        }
                    });
                } else {
                    tableSelect.html('<option value="">-- Select Table --</option>');
                }
            });

            // Open modal for adding new conversation
            $('#btnAddConversation').on('click', function() {
                resetForm();
                $('#modalTitle').html('<i class="bx bx-conversation me-2"></i>Add Conversation');
                $('#formMethod').val('POST');
                $('#btnSubmit').html('<i class="bx bx-check me-1"></i>Save Conversation');
                $('#conversationModal').modal('show');
            });

            // Handle edit button click
            $(document).on('click', '.editBtn', function(e) {
                e.preventDefault();
                const conversationId = $(this).data('id');
                const dataUrl = `{{ auth()->user()->role === 'admin' ? route('conversations.data', ':id') : route('employee.conversations.data', ':id') }}`.replace(':id', conversationId);
                
                $.ajax({
                    url: dataUrl,
                    type: 'GET',
                    success: function(response) {
                        if (response.status && response.data) {
                            const data = response.data;
                            resetForm();
                            
                            // Set form values
                            $('#conversation_id').val(data.id);
                            $('#formMethod').val('PUT');
                            $('#exhibitor_id').val(data.exhibitor_id || '');
                            $('#exhibitor_name').val(data.exhibitor_name || '');
                            $('#visitor_id').val(data.visitor_id).trigger('change');
                            $('#visitor_phone').val(data.visitor_phone || '');
                            $('#campaign_id').val(data.campaign_id).trigger('change');
                            $('#employee_id').val(data.employee_id).trigger('change');
                            $('#outcome').val(data.outcome);
                            $('#notes').val(data.notes || '');
                            $('#conversation_date').val(data.conversation_date || '{{ now()->format('Y-m-d\TH:i') }}');
                            
                            // Load location and table
                            if (data.location_id) {
                                $('#location_id').val(data.location_id).trigger('change');
                                setTimeout(function() {
                                    if (data.table_id) {
                                        $('#table_id').val(data.table_id).trigger('change');
                                    }
                                }, 500);
                            }
                            
                            $('#modalTitle').html('<i class="bx bx-conversation me-2"></i>Edit Conversation');
                            $('#btnSubmit').html('<i class="bx bx-check me-1"></i>Update Conversation');
                            $('#conversationModal').modal('show');
                        }
                    },
                    error: function() {
                        toastr.error('Failed to load conversation data');
                    }
                });
            });

            // Handle form submission
            $('#conversationForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                const conversationId = $('#conversation_id').val();
                const method = $('#formMethod').val();
                let url, submitText;
                
                if (method === 'PUT' && conversationId) {
                    // Update
                    url = `{{ auth()->user()->role === 'admin' ? route('conversations.update', ':id') : route('employee.conversations.update', ':id') }}`.replace(':id', conversationId);
                    submitText = 'Update';
                } else {
                    // Create
                    url = `{{ auth()->user()->role === 'admin' ? route('conversations.store') : route('employee.conversations.store') }}`;
                    submitText = 'Create';
                }
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#conversationModal').modal('hide');
                            conversationsTable.ajax.reload();
                        } else {
                            toastr.error(response.message || 'Failed to ' + submitText.toLowerCase() + ' conversation');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            let errorMsg = 'Validation errors:\n';
                            Object.keys(errors).forEach(key => {
                                errorMsg += `- ${errors[key][0]}\n`;
                            });
                            toastr.error(errorMsg);
                        } else {
                            toastr.error('Failed to ' + submitText.toLowerCase() + ' conversation');
                        }
                    }
                });
            });

            // Reset form function
            function resetForm() {
                $('#conversationForm')[0].reset();
                $('#conversation_id').val('');
                $('#formMethod').val('POST');
                $('#exhibitor_id').val('');
                $('#exhibitor_name').val('');
                $('.select2').val(null).trigger('change');
                $('#table_id').html('<option value="">-- Select Table --</option>');
                $('#conversation_date').val('{{ now()->format('Y-m-d\TH:i') }}');
                
                // Set default employee to current user if employee
                @if(auth()->user()->role === 'employee')
                $('#employee_id').val('{{ auth()->id() }}').trigger('change');
                @endif
            }

            // Delete conversation
            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                if (id) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Are you sure?',
                        text: 'You want to delete this conversation?',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#555',
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.value) {
                            const deleteUrl = '{{ auth()->user()->role === "admin" ? route("conversations.destroy", ":id") : route("employee.conversations.destroy", ":id") }}'.replace(':id', id);
                            $.ajax({
                                url: deleteUrl,
                                type: 'DELETE',
                                data: {
                                    _method: 'DELETE',
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    if (response.status == true) {
                                        toastr.success(response.message);
                                        conversationsTable.ajax.reload();
                                    } else {
                                        toastr.error(response.message);
                                    }
                                },
                                error: function() {
                                    toastr.error('Server error. Please try again.');
                                }
                            });
                        }
                    });
                }
            });

            // Clean up Select2 when modal is hidden
            $('#conversationModal').on('hidden.bs.modal', function() {
                // Reset table select
                $('#table_id').html('<option value="">-- Select Table --</option>');
            });

            // Handle view canvas button click
            $(document).on('click', '.viewCanvasBtn', function(e) {
                e.preventDefault();
                const contactId = $(this).data('contact-id');
                const contactName = $(this).data('contact-name');
                const contactPhone = $(this).data('contact-phone') || '';
                const contactEmail = $(this).data('contact-email') || '';
                const visitorId = $(this).data('visitor-id') || '';
                const exhibitorId = $(this).data('exhibitor-id') || '';
                
                openConversationsCanvas(contactId, contactName, contactPhone, contactEmail, visitorId, exhibitorId);
            });

            // Global function to open conversations canvas
            window.openConversationsCanvas = function(contactId, contactName, contactPhone, contactEmail, visitorId, exhibitorId) {
                try {
                    const canvasElement = document.getElementById('conversationsCanvas');
                    if (!canvasElement) {
                        toastr.error('Conversations panel not found. Please refresh the page.');
                        return;
                    }
                    
                    // Set contact info in canvas header
                    $('#canvasContactName').html(`<i class="bx bx-conversation me-2"></i>Conversations - ${contactName || 'Unknown'}`);
                    $('#canvasContactInfo').html(`${contactPhone || ''} ${contactEmail ? ' • ' + contactEmail : ''}`);
                    
                    // Store contact data for later use
                    $('#conversationsCanvas').data('contact-id', contactId);
                    $('#conversationsCanvas').data('contact-name', contactName);
                    $('#conversationsCanvas').data('contact-phone', contactPhone);
                    $('#conversationsCanvas').data('contact-email', contactEmail);
                    $('#conversationsCanvas').data('visitor-id', visitorId);
                    $('#conversationsCanvas').data('exhibitor-id', exhibitorId);
                    
                    // Show loading state
                    $('#conversationsLoading').show();
                    $('#conversationsList').hide();
                    $('#conversationsEmpty').hide();
                    
                    // Open canvas
                    const canvas = new bootstrap.Offcanvas(canvasElement);
                    canvas.show();
                    
                    // Load conversations after a small delay
                    setTimeout(function() {
                        loadConversationsForContact(visitorId, exhibitorId);
                    }, 300);
                } catch (error) {
                    console.error('Error opening conversations canvas:', error);
                    toastr.error('Error opening conversations panel. Please try again.');
                }
            };

            // Load conversations for a contact
            function loadConversationsForContact(visitorId, exhibitorId) {
                const url = `{{ auth()->user()->role === 'admin' ? route('conversations.forContact') : route('employee.conversations.forContact') }}`;
                const data = {};
                
                if (visitorId) {
                    data.visitor_id = visitorId;
                } else if (exhibitorId) {
                    data.exhibitor_id = exhibitorId;
                } else {
                    toastr.error('Contact information not found');
                    return;
                }
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#conversationsLoading').hide();
                        
                        if (response.status && response.conversations && response.conversations.length > 0) {
                            displayConversations(response.conversations);
                            $('#conversationsList').show();
                            $('#conversationsEmpty').hide();
                        } else {
                            $('#conversationsList').hide();
                            $('#conversationsEmpty').show();
                        }
                    },
                    error: function(xhr) {
                        $('#conversationsLoading').hide();
                        $('#conversationsList').hide();
                        $('#conversationsEmpty').show();
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
                        <div class="card mb-2 shadow-sm border-0" style="border-left: 3px solid var(--sidebar-end) !important; overflow: hidden;">
                            <div class="card-header p-2" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); color: white; border: none;">
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
                const visitorId = $('#conversationsCanvas').data('visitor-id');
                const exhibitorId = $('#conversationsCanvas').data('exhibitor-id');
                const contactName = $('#conversationsCanvas').data('contact-name');
                
                resetForm();
                
                // Pre-fill visitor or exhibitor if available
                if (visitorId) {
                    $('#visitor_id').val(visitorId).trigger('change');
                }
                if (exhibitorId) {
                    $('#exhibitor_id').val(exhibitorId);
                    // Try to set exhibitor name if available
                    const contactName = $('#conversationsCanvas').data('contact-name');
                    if (contactName && !visitorId) {
                        $('#exhibitor_name').val(contactName);
                    }
                }
                
                $('#modalTitle').html('<i class="bx bx-conversation me-2"></i>Add Conversation');
                $('#formMethod').val('POST');
                $('#btnSubmit').html('<i class="bx bx-check me-1"></i>Save Conversation');
                $('#conversationModal').modal('show');
            });
        });
    </script>
@endsection
