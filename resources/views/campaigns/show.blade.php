@extends("layout.master")
@section('title','View Campaign')

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
                                <i class="ph ph-eye me-3 text-primary"></i>Campaign Details
                            </h5>
                            <p class="text-muted mb-0 fs-9">View campaign information and recipients</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('campaigns.edit', $campaign->id) }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="ph ph-pencil me-2"></i>Edit Campaign
                            </a>
                            <a href="{{ route('campaigns.index') }}" class="btn btn-secondary btn-lg shadow-sm">
                                <i class="ph ph-arrow-left me-2"></i>Back to Campaigns
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Campaign Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="ph ph-megaphone me-2 text-primary"></i>Campaign Information
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted mb-1">Campaign Name</label>
                            <p class="mb-3 fw-semibold text-dark">{{ $campaign->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted mb-1">Campaign Type</label>
                            <p class="mb-3">
                                <span class="badge bg-light text-dark px-3 py-2 border">
                                    <i class="ph ph-tag me-1"></i>{{ ucfirst($campaign->type) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted mb-1">Subject</label>
                            <p class="mb-3 fw-semibold text-dark">{{ $campaign->subject }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted mb-1">Status</label>
                            <p class="mb-3">
                                <span class="badge {{ $campaign->status_badge_class }} px-3 py-2">
                                    <i class="ph ph-{{ $campaign->status === 'draft' ? 'file-text' : ($campaign->status === 'sent' ? 'check-circle' : 'clock') }} me-1"></i>
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted mb-1">Created Date</label>
                            <p class="mb-3">{{ $campaign->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        @if($campaign->scheduled_at)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted mb-1">Scheduled Date</label>
                            <p class="mb-3">{{ $campaign->scheduled_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        @endif
                        @if($campaign->sent_at)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted mb-1">Sent Date</label>
                            <p class="mb-3">{{ $campaign->sent_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted mb-1">Message</label>
                            <div class="border rounded p-3 bg-light">
                                <p class="mb-0">{{ $campaign->message }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Statistics -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="ph ph-chart-bar me-2 text-primary"></i>Campaign Statistics
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <div class="text-primary mb-1">
                                    <i class="ph ph-users" style="font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="mb-1 fw-bold text-primary">{{ $campaign->recipient_count }}</h4>
                                <small class="text-muted">Total Recipients</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <div class="text-success mb-1">
                                    <i class="ph ph-check-circle" style="font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="mb-1 fw-bold text-success">{{ $campaign->recipients->where('status', 'sent')->count() }}</h4>
                                <small class="text-muted">Sent</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                <div class="text-warning mb-1">
                                    <i class="ph ph-clock" style="font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="mb-1 fw-bold text-warning">{{ $campaign->recipients->where('status', 'pending')->count() }}</h4>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                                <div class="text-danger mb-1">
                                    <i class="ph ph-x-circle" style="font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="mb-1 fw-bold text-danger">{{ $campaign->recipients->where('status', 'failed')->count() }}</h4>
                                <small class="text-muted">Failed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        @if($campaign->isDraft())
                        <button type="button" class="btn btn-success btn-lg" onclick="sendCampaign('{{ $campaign->id }}')">
                            <i class="ph ph-paper-plane me-2"></i>Send Campaign
                        </button>
                        @endif
                        <a href="{{ route('campaigns.edit', $campaign->id) }}" class="btn btn-primary">
                            <i class="ph ph-pencil me-2"></i>Edit Campaign
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteCampaign('{{ $campaign->id }}')">
                            <i class="ph ph-trash me-2"></i>Delete Campaign
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recipients List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="ph ph-users me-2 text-primary"></i>Campaign Recipients
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">Name</th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">Contact Info</th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">Type</th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">Status</th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaign->recipients as $recipient)
                                <tr class="border-bottom">
                                    <td class="px-3 py-3">
                                        <h6 class="mb-1 fw-semibold text-dark">{{ $recipient->contact->name }}</h6>
                                    </td>
                                    <td class="px-3 py-3">
                                        @if($recipient->contact->type === 'exhibitor')
                                            <small class="text-muted">{{ $recipient->contact->email }}</small>
                                        @else
                                            <small class="text-muted">{{ $recipient->contact->phone }}</small>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-light text-dark px-3 py-2 border">
                                            <i class="ph ph-{{ $recipient->contact->type === 'exhibitor' ? 'storefront' : 'user' }} me-1"></i>
                                            {{ ucfirst($recipient->contact->type) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        @php
                                            $statusClass = match($recipient->status) {
                                                'pending' => 'bg-warning',
                                                'sent' => 'bg-success',
                                                'delivered' => 'bg-info',
                                                'failed' => 'bg-danger',
                                                default => 'bg-light'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} px-3 py-2">
                                            <i class="ph ph-{{ $recipient->status === 'pending' ? 'clock' : ($recipient->status === 'sent' ? 'check-circle' : ($recipient->status === 'delivered' ? 'check-circle' : 'x-circle')) }} me-1"></i>
                                            {{ ucfirst($recipient->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">{{ $recipient->contact->location }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ph ph-users text-muted mb-3" style="font-size: 2rem;"></i>
                                            <h6 class="text-muted mb-2">No recipients found</h6>
                                            <p class="text-muted mb-0">This campaign has no recipients assigned</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title text-white">
                    <i class="ph ph-warning me-2 text-white"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="ph ph-trash text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <h6 class="text-center mb-2">Are you sure?</h6>
                <p class="text-center text-muted mb-0">This action cannot be undone. The campaign will be permanently removed from the system.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="ph ph-x me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDelete">
                    <i class="ph ph-trash me-2"></i>Delete Campaign
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let campaignToDelete = null;

function deleteCampaign(campaignId) {
    campaignToDelete = campaignId;
    $('#deleteModal').modal('show');
}

function sendCampaign(campaignId) {
    if (confirm('Are you sure you want to send this campaign? This action cannot be undone.')) {
        $.ajax({
            url: `/admin/campaigns/${campaignId}/send`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showNotification('error', response.message || 'Something went wrong!');
            }
        });
    }
}

$('#confirmDelete').click(function() {
    if (campaignToDelete) {
        $.ajax({
            url: `/admin/campaigns/${campaignToDelete}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    showNotification('success', response.message);
                    setTimeout(() => {
                        window.location.href = '{{ route("campaigns.index") }}';
                    }, 1000);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showNotification('error', response.message || 'Something went wrong!');
            }
        });
    }
});
</script>
@endpush
