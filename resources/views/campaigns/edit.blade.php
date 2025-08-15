@extends("layout.master")
@section('title','Edit Campaign')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center" style="margin-left: 20px;padding:10px 2px">
                        <div>
                            <h5 class="mb-2 fw-bold text-dark">
                                <i class="ph ph-pencil me-3 text-primary"></i>Edit Campaign
                            </h5>
                            <p class="text-muted mb-0 fs-9">Update campaign information and recipients</p>
                        </div>
                        <a href="{{ route('campaigns.index') }}" class="btn btn-secondary btn-lg shadow-sm">
                            <i class="ph ph-arrow-left me-2"></i>Back to Campaigns
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Form -->
    <form id="campaignForm" action="{{ route('campaigns.update', $campaign->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Campaign Details Section -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ph ph-megaphone me-2 text-primary"></i>Campaign Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Campaign Name -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="ph ph-tag me-2 text-muted"></i>Campaign Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="name" value="{{ $campaign->name }}" placeholder="Enter campaign name" required>
                            </div>

                            <!-- Campaign Subject -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="ph ph-envelope me-2 text-muted"></i>Campaign Subject <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="subject" value="{{ $campaign->subject }}" placeholder="Enter campaign subject" required>
                            </div>

                            <!-- Campaign Type -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="ph ph-tag me-2 text-muted"></i>Campaign Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="type" required>
                                    <option value="">Select campaign type</option>
                                    <option value="email" {{ $campaign->type === 'email' ? 'selected' : '' }}>Email Campaign</option>
                                    <option value="sms" {{ $campaign->type === 'sms' ? 'selected' : '' }}>SMS Campaign</option>
                                    <option value="whatsapp" {{ $campaign->type === 'whatsapp' ? 'selected' : '' }}>WhatsApp Campaign</option>
                                </select>
                            </div>

                            <!-- Scheduled Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="ph ph-calendar me-2 text-muted"></i>Schedule Date (Optional)
                                </label>
                                <input type="datetime-local" class="form-control" name="scheduled_at" 
                                       value="{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '' }}">
                            </div>

                            <!-- Campaign Message -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="ph ph-chat-circle-text me-2 text-muted"></i>Campaign Message <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" name="message" rows="8" placeholder="Enter your campaign message..." required>{{ $campaign->message }}</textarea>
                                <div class="form-text">
                                    <i class="ph ph-info me-1"></i>Maximum 5000 characters allowed
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients Selection Section -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ph ph-users me-2 text-primary"></i>Select Recipients
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Recipient Type Tabs -->
                        <ul class="nav nav-tabs nav-fill mb-3" id="recipientTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="exhibitors-tab" data-bs-toggle="tab" data-bs-target="#exhibitors" type="button" role="tab">
                                    <i class="ph ph-storefront me-1"></i>Exhibitors
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="visitors-tab" data-bs-toggle="tab" data-bs-target="#visitors" type="button" role="tab">
                                    <i class="ph ph-user me-1"></i>Visitors
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="recipientTabsContent">
                            <!-- Exhibitors Tab -->
                            <div class="tab-pane fade show active" id="exhibitors" role="tabpanel">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Select exhibitors to include:</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllExhibitors()">
                                            Select All
                                        </button>
                                    </div>
                                    <div class="recipient-list" style="max-height: 300px; overflow-y: auto;">
                                        @forelse($exhibitors as $exhibitor)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input recipient-checkbox" type="checkbox" 
                                                   name="recipients[]" value="{{ $exhibitor->id }}" 
                                                   id="exhibitor_{{ $exhibitor->id }}"
                                                   {{ in_array($exhibitor->id, $campaign->recipients->pluck('contact_id')->toArray()) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="exhibitor_{{ $exhibitor->id }}">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $exhibitor->name }}</span>
                                                    <small class="text-muted">{{ $exhibitor->email }} • {{ $exhibitor->location }}</small>
                                                </div>
                                            </label>
                                        </div>
                                        @empty
                                        <div class="text-center py-3">
                                            <i class="ph ph-storefront text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2 mb-0">No exhibitors found</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- Visitors Tab -->
                            <div class="tab-pane fade" id="visitors" role="tabpanel">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Select visitors to include:</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllVisitors()">
                                            Select All
                                        </button>
                                    </div>
                                    <div class="recipient-list" style="max-height: 300px; overflow-y: auto;">
                                        @forelse($visitors as $visitor)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input recipient-checkbox" type="checkbox" 
                                                   name="recipients[]" value="{{ $visitor->id }}" 
                                                   id="visitor_{{ $visitor->id }}"
                                                   {{ in_array($visitor->id, $campaign->recipients->pluck('contact_id')->toArray()) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="visitor_{{ $visitor->id }}">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $visitor->name }}</span>
                                                    <small class="text-muted">{{ $visitor->phone }} • {{ $visitor->location }}</small>
                                                </div>
                                            </label>
                                        </div>
                                        @empty
                                        <div class="text-center py-3">
                                            <i class="ph ph-user text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2 mb-0">No visitors found</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recipient Summary -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold text-dark">Selected Recipients:</span>
                                <span class="badge bg-primary" id="selectedCount">0</span>
                            </div>
                            <div class="text-muted small">
                                <i class="ph ph-info me-1"></i>At least one recipient must be selected
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="ph ph-check-circle me-2"></i>Update Campaign
                            </button>
                            <a href="{{ route('campaigns.index') }}" class="btn btn-secondary">
                                <i class="ph ph-x me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Update selected count when checkboxes change
$('.recipient-checkbox').on('change', function() {
    updateSelectedCount();
});

function updateSelectedCount() {
    const selectedCount = $('.recipient-checkbox:checked').length;
    $('#selectedCount').text(selectedCount);
}

function selectAllExhibitors() {
    $('#exhibitors .recipient-checkbox').prop('checked', true);
    updateSelectedCount();
}

function selectAllVisitors() {
    $('#visitors .recipient-checkbox').prop('checked', true);
    updateSelectedCount();
}

// Form submission
$('#campaignForm').on('submit', function(e) {
    e.preventDefault();
    
    const selectedRecipients = $('.recipient-checkbox:checked').length;
    if (selectedRecipients === 0) {
        showNotification('error', 'Please select at least one recipient for the campaign.');
        return;
    }

    // Show loading state
    $('#submitBtn').prop('disabled', true).html('<i class="ph ph-spinner me-2"></i>Updating...');

    // Submit form via AJAX
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1000);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response.errors) {
                let errorMessage = 'Please fix the following errors:\n';
                Object.keys(response.errors).forEach(key => {
                    errorMessage += `• ${response.errors[key][0]}\n`;
                });
                showNotification('error', errorMessage);
            } else {
                showNotification('error', response.message || 'Something went wrong!');
            }
        },
        complete: function() {
            // Reset loading state
            $('#submitBtn').prop('disabled', false).html('<i class="ph ph-check-circle me-2"></i>Update Campaign');
        }
    });
});

// Initialize selected count
updateSelectedCount();
</script>
@endpush
