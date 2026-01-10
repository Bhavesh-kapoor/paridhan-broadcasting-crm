@extends('layouts.app_layout')
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Conversations</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('conversations.index') }}">All Conversations</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Conversation</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="bx bx-edit me-2 text-primary"></i>Edit Conversation
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="conversationForm" method="POST" action="{{ route('conversations.update', $conversation->id) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <!-- Company & Visitor Information -->
                                    <div class="col-lg-6">
                                        <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                            <div class="card-body p-3">
                                                <h6 class="mb-3 fw-semibold d-flex align-items-center">
                                                    <i class="bx bx-building text-primary me-2"></i>Company & Visitor
                                                </h6>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-store-alt me-1"></i>Exhibitor (Company) <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="exhibitor_id" id="exhibitor_id" class="form-select select2" required>
                                                        <option value="">-- Select Exhibitor --</option>
                                                        @foreach($exhibitors as $exhibitor)
                                                            <option value="{{ $exhibitor->id }}" {{ $conversation->exhibitor_id == $exhibitor->id ? 'selected' : '' }}>{{ $exhibitor->name }}</option>
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
                                                            <option value="{{ $visitor->id }}" {{ $conversation->visitor_id == $visitor->id ? 'selected' : '' }}>{{ $visitor->name }} ({{ $visitor->phone }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-phone me-1"></i>Visitor Phone (if not in contacts)
                                                    </label>
                                                    <input type="text" class="form-control" name="visitor_phone" id="visitor_phone" value="{{ $conversation->visitor_phone }}" placeholder="Enter phone number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Employee & Campaign Information -->
                                    <div class="col-lg-6">
                                        <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                            <div class="card-body p-3">
                                                <h6 class="mb-3 fw-semibold d-flex align-items-center">
                                                    <i class="bx bx-user-circle text-primary me-2"></i>Employee & Campaign
                                                </h6>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-user-check me-1"></i>Handling Employee <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="employee_id" id="employee_id" class="form-select select2" required>
                                                        <option value="">-- Select Employee --</option>
                                                        @foreach($employees as $employee)
                                                            <option value="{{ $employee->id }}" {{ $conversation->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-megaphone me-1"></i>Source Campaign
                                                    </label>
                                                    <select name="campaign_id" id="campaign_id" class="form-select select2">
                                                        <option value="">-- Select Campaign --</option>
                                                        @foreach($campaigns as $campaign)
                                                            <option value="{{ $campaign->id }}" {{ $conversation->campaign_id == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location & Table Information -->
                                    <div class="col-lg-6">
                                        <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                            <div class="card-body p-3">
                                                <h6 class="mb-3 fw-semibold d-flex align-items-center">
                                                    <i class="bx bx-map text-primary me-2"></i>Location & Stall
                                                </h6>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-map-pin me-1"></i>Location
                                                    </label>
                                                    <select name="location_id" id="location_id" class="form-select select2">
                                                        <option value="">-- Select Location --</option>
                                                        @foreach($locations as $location)
                                                            <option value="{{ $location->id }}" {{ $conversation->location_id == $location->id ? 'selected' : '' }}>{{ $location->loc_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-table me-1"></i>Stall/Table
                                                    </label>
                                                    <select name="table_id" id="table_id" class="form-select select2">
                                                        <option value="">-- Select Table --</option>
                                                        @foreach($tables as $table)
                                                            <option value="{{ $table->id }}" {{ $conversation->table_id == $table->id ? 'selected' : '' }}>{{ $table->table_no }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Conversation Details -->
                                    <div class="col-lg-6">
                                        <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                            <div class="card-body p-3">
                                                <h6 class="mb-3 fw-semibold d-flex align-items-center">
                                                    <i class="bx bx-conversation text-primary me-2"></i>Conversation Details
                                                </h6>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-info-circle me-1"></i>Outcome <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="outcome" id="outcome" class="form-select" required>
                                                        <option value="">-- Select Outcome --</option>
                                                        <option value="busy" {{ $conversation->outcome == 'busy' ? 'selected' : '' }}>Busy</option>
                                                        <option value="interested" {{ $conversation->outcome == 'interested' ? 'selected' : '' }}>Interested</option>
                                                        <option value="materialised" {{ $conversation->outcome == 'materialised' ? 'selected' : '' }}>Materialised</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-calendar me-1"></i>Conversation Date
                                                    </label>
                                                    <input type="datetime-local" class="form-control" name="conversation_date" id="conversation_date" 
                                                        value="{{ $conversation->conversation_date ? $conversation->conversation_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                            <div class="card-body p-3">
                                                <h6 class="mb-3 fw-semibold d-flex align-items-center">
                                                    <i class="bx bx-note me-2 text-primary"></i>Notes
                                                </h6>
                                                <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Enter conversation notes/comments...">{{ $conversation->notes }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('conversations.index') }}" class="btn btn-outline-secondary">
                                                <i class="bx bx-arrow-back me-1"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-check-circle me-1"></i>Update Conversation
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Load tables when location changes
            $('#location_id').on('change', function() {
                const locationId = $(this).val();
                const tableSelect = $('#table_id');
                
                if (locationId) {
                    $.ajax({
                        url: `{{ route('conversations.getTables', ':id') }}`.replace(':id', locationId),
                        type: 'GET',
                        success: function(response) {
                            if (Array.isArray(response) && response.length > 0) {
                                const currentTableId = '{{ $conversation->table_id }}';
                                tableSelect.html('<option value="">-- Select Table --</option>');
                                response.forEach(function(table) {
                                    const selected = table.id == currentTableId ? 'selected' : '';
                                    tableSelect.append(`<option value="${table.id}" ${selected}>${table.table_no} (₹${table.price || 0})</option>`);
                                });
                            }
                        },
                        error: function() {
                            toastr.error('Failed to load tables');
                        }
                    });
                }
            });

            // Form submission
            $('#conversationForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            window.location.href = '{{ route('conversations.index') }}';
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
                            toastr.error('Failed to update conversation');
                        }
                    }
                });
            });
        });
    </script>
@endsection



