@extends('layouts.app_layout')

@section('title', 'Campaign Conversations')

@section('style')
    <link rel="stylesheet" href="{{ asset('/assets/css/enhanced-tables.css') }}">
    <style>
        .recipient-card {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .recipient-card:hover {
            border-left-color: var(--sidebar-end, #3b82f6);
            transform: translateX(5px);
        }
        
        .recipient-card.has-conversation {
            border-left-color: #22c55e;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }
        
        .recipient-card.no-conversation {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, #fef3c7 0%, #ffffff 100%);
        }
    </style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Campaign Conversations</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            @if(auth()->user()->role == 'admin')
                            <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('campaigns.show', $campaign->id) }}">{{ $campaign->name }}</a></li>
                            @else
                            <li class="breadcrumb-item"><a href="{{ route('employee.campaigns.index') }}">Campaigns</a></li>
                            @endif
                            <li class="breadcrumb-item active">Conversations</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Campaign Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--sidebar-start, #1e3a8a) 0%, var(--sidebar-end, #3b82f6) 100%); border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="text-white mb-1">
                                        <i class="bx bx-megaphone me-2"></i>{{ $campaign->name }}
                                    </h4>
                                    <p class="text-white-50 mb-0">{{ $campaign->subject }}</p>
                                </div>
                                <div class="text-end text-white">
                                    <div class="d-flex gap-3">
                                        <div>
                                            <small class="opacity-75">Recipients</small>
                                            <h5 class="mb-0">{{ $recipients->count() }}</h5>
                                        </div>
                                        <div>
                                            <small class="opacity-75">Conversations</small>
                                            <h5 class="mb-0">{{ $conversations->count() }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Recipients Section (People who received messages) -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bx bx-user-check me-2" style="color: var(--sidebar-end, #3b82f6);"></i>Campaign Recipients
                                </h5>
                                <small class="text-muted">People who received messages - Click to initiate conversation</small>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($recipients->count() > 0)
                            <div class="row g-3">
                                @foreach($recipients as $recipient)
                                @php
                                    $contact = $recipient->contact;
                                    $existingConversation = $conversations->where('visitor_id', $contact->id ?? null)->first();
                                    $hasConversation = $existingConversation ? true : false;
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="card recipient-card {{ $hasConversation ? 'has-conversation' : 'no-conversation' }}">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold">
                                                        <i class="bx bx-user me-1"></i>{{ $contact->name ?? $recipient->email ?? $recipient->phone ?? 'Unknown' }}
                                                    </h6>
                                                    @if($contact)
                                                    <small class="text-muted d-block">
                                                        <i class="bx bx-phone me-1"></i>{{ $contact->phone ?? 'N/A' }}
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class="bx bx-envelope me-1"></i>{{ $contact->email ?? $recipient->email ?? 'N/A' }}
                                                    </small>
                                                    @else
                                                    <small class="text-muted d-block">
                                                        <i class="bx bx-phone me-1"></i>{{ $recipient->phone ?? 'N/A' }}
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class="bx bx-envelope me-1"></i>{{ $recipient->email ?? 'N/A' }}
                                                    </small>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($hasConversation)
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-check"></i> Done
                                                    </span>
                                                    @else
                                                    <span class="badge bg-warning">
                                                        <i class="bx bx-clock"></i> Pending
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <small class="text-muted">
                                                    <i class="bx bx-send me-1"></i>
                                                    @if($recipient->sent_at)
                                                        Sent: {{ $recipient->sent_at->format('M d, Y H:i') }}
                                                    @else
                                                        Not sent yet
                                                    @endif
                                                </small>
                                                @if($hasConversation)
                                                <small class="text-success">
                                                    <i class="bx bx-conversation me-1"></i>Conversation exists
                                                </small>
                                                @endif
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="mt-3 pt-2 border-top">
                                                <div class="d-flex gap-2 flex-wrap">
                                                    @if($hasConversation)
                                                    <a href="{{ route('campaigns.conversations', $campaign->id) }}" class="btn btn-sm btn-info flex-fill" title="View Conversation">
                                                        <i class="bx bx-show me-1"></i>View
                                                    </a>
                                                    @php
                                                        $conversation = $conversations->where('visitor_id', $contact->id ?? null)->first();
                                                    @endphp
                                                    @if($conversation && $conversation->booking)
                                                    <a href="{{ route('conversations.invoice', $conversation->id) }}" class="btn btn-sm btn-warning flex-fill" title="View Invoice" target="_blank">
                                                        <i class="bx bx-file me-1"></i>Invoice
                                                    </a>
                                                    @endif
                                                    @else
                                                    <button class="btn btn-sm btn-primary flex-fill" 
                                                            onclick="openConversationModalForRecipient('{{ $contact->id ?? '' }}', '{{ addslashes($contact->name ?? $recipient->email ?? $recipient->phone ?? 'Unknown') }}', '{{ $recipient->id }}', false); return false;"
                                                            title="Initiate Conversation"
                                                            type="button">
                                                        <i class="bx bx-conversation me-1"></i>Start Conversation
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="bx bx-user-x fs-1 text-muted"></i>
                                <p class="text-muted mt-3">No recipients found for this campaign.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Conversations Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bx bx-conversation me-2" style="color: var(--sidebar-end, #3b82f6);"></i>Existing Conversations
                                </h5>
                                <a href="{{ route('campaigns.conversations.create', $campaign->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-plus me-1"></i>Add Conversation
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($conversations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-header-gradient">
                                        <tr>
                                            <th><i class="bx bx-building me-1"></i>Exhibitor</th>
                                            <th><i class="bx bx-user me-1"></i>Visitor</th>
                                            <th><i class="bx bx-user-circle me-1"></i>Employee</th>
                                            <th><i class="bx bx-map me-1"></i>Location</th>
                                            <th><i class="bx bx-table me-1"></i>Table</th>
                                            <th class="text-center"><i class="bx bx-info-circle me-1"></i>Outcome</th>
                                            <th><i class="bx bx-calendar me-1"></i>Date</th>
                                            <th class="text-center"><i class="bx bx-cog me-1"></i>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($conversations as $conversation)
                                        <tr>
                                            <td>{{ $conversation->exhibitor->name ?? 'N/A' }}</td>
                                            <td>{{ $conversation->visitor->name ?? $conversation->visitor_phone ?? 'N/A' }}</td>
                                            <td>{{ $conversation->employee->name ?? 'N/A' }}</td>
                                            <td>{{ $conversation->location->loc_name ?? 'N/A' }}</td>
                                            <td>{{ $conversation->table->table_no ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                @php
                                                    $badgeClass = match($conversation->outcome) {
                                                        'materialised' => 'bg-success',
                                                        'busy' => 'bg-warning',
                                                        'interested' => 'bg-info',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($conversation->outcome) }}</span>
                                            </td>
                                            <td>{{ $conversation->conversation_date->format('M d, Y H:i') }}</td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if(auth()->user()->role == 'admin')
                                                    <a href="{{ route('conversations.show', $conversation->id) }}" class="btn btn-sm btn-action btn-view" title="View">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                    @endif
                                                    @if($conversation->booking)
                                                    <a href="{{ route('conversations.invoice', $conversation->id) }}" class="btn btn-sm btn-action btn-success" title="Generate Invoice" target="_blank">
                                                        <i class="bx bx-file"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="bx bx-conversation fs-1 text-muted"></i>
                                <p class="text-muted mt-3">No conversations found for this campaign.</p>
                                <p class="text-muted">Click on a recipient card above to initiate a conversation.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversation Modal for Recipient -->
    <div class="modal fade" id="recipientConversationModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--sidebar-start, #1e3a8a) 0%, var(--sidebar-end, #3b82f6) 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title">
                        <i class="bx bx-conversation me-2"></i>Initiate Conversation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="recipientConversationForm">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                        <input type="hidden" name="campaign_recipient_id" id="modal_campaign_recipient_id">
                        <input type="hidden" name="visitor_id" id="modal_visitor_id">
                        
                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Recipient:</strong> <span id="modal_recipient_name"></span>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Exhibitor (Company) <span class="text-danger">*</span></label>
                                <select name="exhibitor_id" id="modal_exhibitor_id" class="form-select" required>
                                    <option value="">-- Select Exhibitor --</option>
                                    @foreach($exhibitors as $exhibitor)
                                        <option value="{{ $exhibitor->id }}">{{ $exhibitor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Location</label>
                                <select name="location_id" id="modal_location_id" class="form-select select2">
                                    <option value="">-- Select Location --</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->loc_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Stall/Table <small class="text-muted">(Searchable - handles 2000+ tables)</small></label>
                                <select name="table_id" id="modal_table_id" class="form-select select2-table">
                                    <option value="">-- Select Table --</option>
                                </select>
                                <small class="text-muted">Select location first, then search and select table</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Outcome <span class="text-danger">*</span></label>
                                <select name="outcome" id="modal_outcome" class="form-select" required>
                                    <option value="">-- Select Outcome --</option>
                                    <option value="busy">Busy</option>
                                    <option value="interested">Interested</option>
                                    <option value="materialised">Materialised</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Conversation Date</label>
                                <input type="datetime-local" class="form-control" name="conversation_date" id="modal_conversation_date" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control" name="notes" id="modal_notes" rows="3" placeholder="Enter conversation notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i>Create Conversation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function openConversationModalForRecipient(contactId, recipientName, recipientId, hasConversation) {
            try {
                console.log('Opening conversation modal:', {contactId, recipientName, recipientId, hasConversation});
                
                if (hasConversation) {
                    toastr.info('Conversation already exists for this recipient');
                    return;
                }
                
                if (!recipientId) {
                    toastr.error('Recipient ID is missing. Please refresh the page.');
                    return;
                }
                
                // Set form values
                $('#modal_visitor_id').val(contactId || '');
                $('#modal_recipient_name').text(recipientName || 'Unknown');
                $('#modal_campaign_recipient_id').val(recipientId || '');
                
                // Reset exhibitor dropdown
                $('#modal_exhibitor_id').val('').trigger('change');
                
                // Reset location dropdown
                if ($('#modal_location_id').hasClass('select2-hidden-accessible')) {
                    $('#modal_location_id').val('').trigger('change');
                } else {
                    $('#modal_location_id').val('');
                }
                
                // Reset table dropdown
                if ($('#modal_table_id').hasClass('select2-hidden-accessible')) {
                    $('#modal_table_id').select2('destroy');
                }
                $('#modal_table_id').empty().append('<option value="">-- Select Table --</option>');
                
                // Reset other fields
                $('#modal_outcome').val('');
                $('#modal_notes').val('');
                $('#modal_conversation_date').val(new Date().toISOString().slice(0, 16));
                
                // Initialize Select2 for location if not already done
                if (!$('#modal_location_id').hasClass('select2-hidden-accessible')) {
                    $('#modal_location_id').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dropdownParent: $('#recipientConversationModal')
                    });
                }
                
                // Initialize Select2 for table dropdown (empty state)
                $('#modal_table_id').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#recipientConversationModal'),
                    placeholder: '-- Select Location First --'
                });
                
                // Show modal - use same pattern as other modals in the codebase
                const modalElement = document.getElementById('recipientConversationModal');
                if (modalElement) {
                    new bootstrap.Modal(modalElement).show();
                } else {
                    console.error('Modal element not found');
                    toastr.error('Error: Conversation modal not found. Please refresh the page.');
                }
            } catch (error) {
                console.error('Error opening conversation modal:', error);
                toastr.error('Error opening conversation form. Please try again.');
            }
        }
        
        // Make function globally accessible
        window.openConversationModalForRecipient = openConversationModalForRecipient;

        // Initialize Select2 for location dropdown when modal opens
        $(document).ready(function() {
            // Initialize location select2 if modal is shown
            $('#recipientConversationModal').on('shown.bs.modal', function() {
                // Ensure location select2 is initialized
                if (!$('#modal_location_id').hasClass('select2-hidden-accessible')) {
                    $('#modal_location_id').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dropdownParent: $('#recipientConversationModal')
                    });
                }
                
                // Ensure table select2 is initialized
                if (!$('#modal_table_id').hasClass('select2-hidden-accessible')) {
                    $('#modal_table_id').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dropdownParent: $('#recipientConversationModal'),
                        placeholder: '-- Select Location First --'
                    });
                }
            });
            
            // Clean up Select2 when modal is hidden
            $('#recipientConversationModal').on('hidden.bs.modal', function() {
                // Don't destroy, just reset values
                $('#modal_location_id').val('').trigger('change');
                if ($('#modal_table_id').hasClass('select2-hidden-accessible')) {
                    $('#modal_table_id').val('').trigger('change');
                }
            });
        });

        // Initialize Select2 for table dropdown with AJAX (handles large datasets)
        function initTableSelect2(locationId) {
            $('#modal_table_id').select2('destroy');
            $('#modal_table_id').empty().append('<option value="">-- Select Table --</option>');
            
            if (!locationId) {
                $('#modal_table_id').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#recipientConversationModal')
                });
                return;
            }
            
            $('#modal_table_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Search and Select Table --',
                allowClear: true,
                dropdownParent: $('#recipientConversationModal'),
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
        $(document).on('change', '#modal_location_id', function() {
            const locationId = $(this).val();
            initTableSelect2(locationId);
        });

        // Handle form submission
        $('#recipientConversationForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '{{ route("campaigns.conversations.store", $campaign->id) }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        bootstrap.Modal.getInstance(document.getElementById('recipientConversationModal')).hide();
                        // Reload page to show new conversation
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
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
    </script>
@endsection
