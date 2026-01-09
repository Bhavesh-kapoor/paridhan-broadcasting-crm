@extends("layout.master")
@section('title', $title)

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
                                <i class="ph ph-{{ $type === 'exhibitor' ? 'storefront' : 'user-circle' }} me-3 text-{{ $type === 'exhibitor' ? 'primary' : 'success' }}"></i>{{ $title }}
                            </h5>
                            <p class="text-muted mb-0 fs-9">Manage {{ $type === 'exhibitor' ? 'exhibitor' : 'visitor' }} information and details</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('contacts.create', ['type' => $type]) }}" class="btn btn-{{ $type === 'exhibitor' ? 'primary' : 'success' }} btn-lg shadow-sm">
                                <i class="ph ph-plus-circle me-2"></i>Add {{ ucfirst($type) }}
                            </a>
                            <a href="{{ route(Auth::user()->role . '.dashboard')}}" class="btn btn-secondary btn-lg">
                                <i class="ph ph-arrow-left me-2"></i>Dashboard
                            </a>
                        </div>
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
                                <i class="ph ph-magnifying-glass me-1"></i>Search {{ ucfirst($type) }}s
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm border-start-0 py-1" id="searchInput"
                                       placeholder="Search by name, location, or phone..."
                                       style="border-left: none;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-1 small">
                                <i class="ph ph-map-pin me-1"></i>Location Filter
                            </label>
                            <select class="form-select form-select-sm py-1" id="locationFilter">
                                <option value="">All Locations</option>
                                <option value="mumbai">Mumbai</option>
                                <option value="delhi">Delhi</option>
                                <option value="bangalore">Bangalore</option>
                            </select>
                        </div>
                        <!-- <div class="col-md-4">
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
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contacts Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3" style="margin-top: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ph ph-list me-2 text-{{ $type === 'exhibitor' ? 'primary' : 'success' }}"></i>{{ ucfirst($type) }} List
                        </h6>
                        <span class="badge bg-{{ $type === 'exhibitor' ? 'primary' : 'success' }} px-3 py-2">
                            <i class="ph ph-{{ $type === 'exhibitor' ? 'storefront' : 'user-circle' }} me-1"></i>{{ $contacts->total() }} {{ ucfirst($type) }}s
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="contactsTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">
                                        <i class="bx bx-hash"></i> #
                                    </th>
                                    <th>
                                        <i class="bx bx-{{ $type === 'exhibitor' ? 'store-alt' : 'user-circle' }}"></i> {{ ucfirst($type) }} Name
                                    </th>
                                    <th>
                                        <i class="bx bx-map"></i> Location
                                    </th>
                                    <th>
                                        <i class="bx bx-phone"></i> Contact Info
                                    </th>
                                    @if($type === 'exhibitor')
                                    <th>
                                        <i class="bx bx-package"></i> Business Info
                                    </th>
                                    @endif
                                    <th class="text-center">
                                        <i class="bx bx-cog"></i> Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $index => $contact)
                                <tr class="border-bottom">
                                    <td class="px-3 py-3 text-center">
                                        <span class="badge bg-light text-dark px-2 py-1 fw-semibold">
                                            {{ $contacts->firstItem() + $index }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-1 fw-semibold text-dark fs-6">{{ $contact->name }}</h6>
                                                <span class="badge bg-{{ $type === 'exhibitor' ? 'primary' : 'success' }} rounded-pill  border-0 shadow-sm" style="padding:10px !important;">
                                                    {{ $contact->type_display_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ph ph-map-pin me-2 text-muted"></i>
                                            <span class="fw-medium text-dark">{{ $contact->location }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="d-flex flex-column gap-2">
                                            @if($contact->phone)
                                            <div class="d-flex align-items-center">
                                                <i class="ph ph-phone me-2 text-muted"></i>
                                                <small class="text-dark">{{ $contact->phone }}</small>
                                            </div>
                                            @endif
                                            @if($contact->alternate_phone)
                                            <div class="d-flex align-items-center">
                                                <i class="ph ph-phone me-2 text-muted"></i>
                                                <small class="text-dark">{{ $contact->alternate_phone }}</small>
                                            </div>
                                            @endif
                                            @if($contact->email)
                                            <div class="d-flex align-items-center">
                                                <i class="ph ph-envelope me-2 text-muted"></i>
                                                <small class="text-dark">{{ $contact->email }}</small>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    @if($type === 'exhibitor')
                                    <td class="px-3 py-3">
                                        <div class="d-flex flex-column gap-1">
                                            @if($contact->product_type)
                                            <div class="d-flex align-items-center">
                                                <i class="ph ph-package me-2 text-muted"></i>
                                                <small class="text-dark">{{ $contact->product_type }}</small>
                                            </div>
                                            @endif
                                            @if($contact->brand_name)
                                            <div class="d-flex align-items-center">
                                                <i class="ph ph-tag me-2 text-muted"></i>
                                                <small class="text-dark">{{ $contact->brand_name }}</small>
                                            </div>
                                            @endif
                                            @if($contact->business_type)
                                            <div class="d-flex align-items-center">
                                                <i class="ph ph-buildings me-2 text-muted"></i>
                                                <small class="text-dark">{{ $contact->business_type }}</small>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    @endif
                                    <td class="px-3 py-3 text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if($type === 'exhibitor')
                                            <a href="{{ route('admin.companies.dashboard', $contact->id) }}"
                                               class="btn btn-action btn-view"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="View Company Dashboard">
                                                <i class="bx bx-line-chart"></i>
                                                <span class="d-none d-md-inline">Dashboard</span>
                                            </a>
                                            @endif
                                            <a href="{{ route('contacts.edit', $contact) }}"
                                               class="btn btn-action btn-edit"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="Edit {{ ucfirst($type) }}">
                                                <i class="bx bx-edit"></i>
                                                <span class="d-none d-md-inline">Edit</span>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-action btn-delete"
                                                    onclick="deleteContact('{{ $contact->id }}')"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Delete {{ ucfirst($type) }}">
                                                <i class="bx bx-trash"></i>
                                                <span class="d-none d-md-inline">Delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $type === 'exhibitor' ? '6' : '5' }}" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                                                 style="width: 80px; height: 80px;">
                                                <i class="ph ph-{{ $type === 'exhibitor' ? 'storefront' : 'user-circle' }} text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="text-muted mb-2">No {{ $type }}s found</h5>
                                            <p class="text-muted mb-3">Get started by adding your first {{ $type }}</p>
                                            <a href="{{ route('contacts.create', ['type' => $type]) }}" class="btn btn-{{ $type === 'exhibitor' ? 'primary' : 'success' }} btn-lg shadow-sm">
                                                <i class="ph ph-plus-circle me-2"></i>Add First {{ ucfirst($type) }}
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
    @if($contacts->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $contacts->firstItem() ?? 0 }} to {{ $contacts->lastItem() ?? 0 }} of {{ $contacts->total() }} entries
                        </div>
                        <div>
                            {{ $contacts->appends(['type' => $type])->links() }}
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
                <p class="text-center text-muted mb-0">This action cannot be undone. The {{ $type }} will be permanently removed from the system.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="ph ph-x me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDelete">
                    <i class="ph ph-trash me-2"></i>Delete {{ ucfirst($type) }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let contactToDelete = null;

// Search functionality
$('#searchInput').on('keyup', function() {
    applyFilters();
});

// Location filter functionality
$('#locationFilter').on('change', function() {
    applyFilters();
});

// Combined filter function
function applyFilters() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const selectedLocation = $('#locationFilter').val().toLowerCase();

    $('#contactsTable tbody tr').each(function() {
        const contactName = $(this).find('td:nth-child(2) h6').text().toLowerCase();
        const contactLocation = $(this).find('td:nth-child(3) span').text().toLowerCase();

        // Check if row matches search term
        const matchesSearch = !searchTerm ||
            contactName.includes(searchTerm);

        // Check if row matches location filter
        const matchesLocation = !selectedLocation || contactLocation.includes(selectedLocation);

        // Show row only if it matches both filters
        if (matchesSearch && matchesLocation) {
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
    $('#locationFilter').val('');
    $('#contactsTable tbody tr').show();
});

function deleteContact(contactId) {
    contactToDelete = contactId;
    $('#deleteModal').modal('show');
}

$('#confirmDelete').click(function() {
    if (contactToDelete) {
        $.ajax({
            url: `/admin/contacts/${contactToDelete}`,
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
