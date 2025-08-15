@extends("layout.master")
@section('title','Campaign Management')

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
                                <i class="ph ph-megaphone me-3 text-primary"></i>Campaign Management
                            </h5>
                            <p class="text-muted mb-0 fs-9">Manage your marketing campaigns and messaging</p>
                        </div>
                        <a href="{{ route('campaigns.create') }}" class="btn btn-primary btn-lg shadow-sm">
                            <i class="ph ph-plus-circle me-2"></i>Create Campaign
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="row mb-4" style="margin-top:20px;">
        <div class="col-12">
            <div class="card border-0">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-end" style="padding:10px">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-muted mb-1 small">
                                <i class="ph ph-magnifying-glass me-1"></i>Search Campaigns
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm border-start-0 py-1" id="searchInput" 
                                       placeholder="Search by name or subject..." 
                                       style="border-left: none;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-1 small">
                                <i class="ph ph-check-circle me-1"></i>Status Filter
                            </label>
                            <select class="form-select form-select-sm py-1" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="draft">Draft</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="sent">Sent</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-muted mb-1 small">
                                <i class="ph ph-gear me-1"></i>Actions
                            </label>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-primary btn-sm py-1 px-2" id="filterBtn">
                                    <i class="ph ph-funnel me-1 small"></i>Apply Filter
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm py-1 px-2" id="resetBtn">
                                    <i class="ph ph-arrow-clockwise me-1 small"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3" style="margin-top: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ph ph-list me-2 text-primary"></i>Campaign List
                        </h6>
                        <span class="badge bg-primary px-3 py-2">
                            <i class="ph ph-megaphone me-1"></i>{{ $campaigns->total() }} Campaigns
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="campaignsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark text-center" style="width: 60px;">
                                        <i class="ph ph-hash me-1 text-muted"></i>#
                                    </th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">
                                        <i class="ph ph-megaphone me-2 text-muted"></i>Campaign
                                    </th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">
                                        <i class="ph ph-users me-2 text-muted"></i>Recipients
                                    </th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">
                                        <i class="ph ph-tag me-2 text-muted"></i>Type
                                    </th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">
                                        <i class="ph ph-check-circle me-2 text-muted"></i>Status
                                    </th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark">
                                        <i class="ph ph-calendar me-2 text-muted"></i>Created
                                    </th>
                                    <th class="border-0 px-3 py-3 fw-semibold text-dark text-center">
                                        <i class="ph ph-gear me-2 text-muted"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $index => $campaign)
                                <tr class="border-bottom">
                                    <td class="px-3 py-3 text-center">
                                        <span class="badge bg-light text-dark px-2 py-1 fw-semibold">
                                            {{ $campaigns->firstItem() + $index }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 fw-semibold text-dark fs-6">{{ $campaign->name }}</h6>
                                            <small class="text-muted">{{ $campaign->subject }}</small>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-info px-3 py-2">
                                            <i class="ph ph-users me-1"></i>{{ $campaign->recipient_count }} Recipients
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-light text-dark px-3 py-2 border">
                                            <i class="ph ph-tag me-1"></i>{{ ucfirst($campaign->type) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge {{ $campaign->status_badge_class }} px-3 py-2">
                                            <i class="ph ph-{{ $campaign->status === 'draft' ? 'file-text' : ($campaign->status === 'sent' ? 'check-circle' : 'clock') }} me-1"></i>
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">{{ $campaign->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="btn-group btn-group-sm shadow-sm" role="group">
                                            <a href="{{ route('campaigns.show', $campaign->id) }}" 
                                               class="btn btn-info btn-sm border" 
                                               title="View Campaign"
                                               style="min-width: 40px;">
                                                <i class="ph ph-eye"></i>
                                            </a>
                                            <a href="{{ route('campaigns.edit', $campaign->id) }}" 
                                               class="btn btn-primary btn-sm border" 
                                               title="Edit Campaign"
                                               style="min-width: 40px;">
                                                <i class="ph ph-pencil"></i>
                                            </a>
                                            @if($campaign->isDraft())
                                            <button type="button" 
                                                    class="btn btn-success btn-sm border" 
                                                    onclick="sendCampaign('{{ $campaign->id }}')"
                                                    title="Send Campaign"
                                                    style="min-width: 40px;">
                                                <i class="ph ph-paper-plane"></i>
                                            </button>
                                            @endif
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm border" 
                                                    onclick="deleteCampaign('{{ $campaign->id }}')"
                                                    title="Delete Campaign"
                                                    style="min-width: 40px;">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" 
                                                 style="width: 80px; height: 80px;">
                                                <i class="ph ph-megaphone text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="text-muted mb-2">No campaigns found</h5>
                                            <p class="text-muted mb-3">Get started by creating your first campaign</p>
                                            <a href="{{ route('campaigns.create') }}" class="btn btn-primary btn-lg shadow-sm">
                                                <i class="ph ph-plus-circle me-2"></i>Create First Campaign
                                            </a>
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

    <!-- Pagination Section -->
    @if($campaigns->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $campaigns->firstItem() ?? 0 }} to {{ $campaigns->lastItem() ?? 0 }} of {{ $campaigns->total() }} entries
                        </div>
                        <div>
                            {{ $campaigns->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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

// Search functionality
$('#searchInput').on('keyup', function() {
    applyFilters();
});

// Status filter functionality
$('#statusFilter').on('change', function() {
    applyFilters();
});

// Combined filter function
function applyFilters() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const selectedStatus = $('#statusFilter').val().toLowerCase();
    
    $('#campaignsTable tbody tr').each(function() {
        const campaignName = $(this).find('td:nth-child(2) h6').text().toLowerCase();
        const campaignSubject = $(this).find('td:nth-child(2) small').text().toLowerCase();
        const statusBadge = $(this).find('td:nth-child(5) .badge');
        const status = statusBadge.text().toLowerCase().replace(/\s+/g, '');
        
        // Check if row matches search term
        const matchesSearch = !searchTerm || 
            campaignName.includes(searchTerm) || 
            campaignSubject.includes(searchTerm);
        
        // Check if row matches status filter
        const matchesStatus = !selectedStatus || status.includes(selectedStatus);
        
        // Show row only if it matches both filters
        if (matchesSearch && matchesStatus) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

// Filter button
$('#filterBtn').on('click', function() {
    applyFilters();
});

// Reset button
$('#resetBtn').on('click', function() {
    $('#searchInput').val('');
    $('#statusFilter').val('');
    $('#campaignsTable tbody tr').show();
});

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
});

// Notification function is now available globally from master layout
</script>
@endpush
