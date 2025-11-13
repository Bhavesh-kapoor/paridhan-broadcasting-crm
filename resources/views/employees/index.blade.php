@extends('layout.master')
@section('title', 'Employees Management')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center"
                            style="margin-left: 20px;padding:10px 2px">
                            <div>
                                <h5 class="mb-2 fw-bold text-dark">
                                    <i class="ph ph-users me-3 text-primary"></i>Employees Management
                                </h5>
                                <p class="text-muted mb-0 fs-9">Manage your team members and their information</p>
                            </div>
                            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="ph ph-plus-circle me-2"></i>Add New Employee
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-4" style="margin-top:20px;">
            <div class="col-12">
                <div class="card border-0 ">

                    <div class="card-body p-3">
                        <div class="row g-2 align-items-end" style="padding:10px">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-muted mb-1 small">
                                    <i class="ph ph-magnifying-glass me-1"></i>Search Employees
                                </label>
                                <div class="input-group input-group-sm">

                                    <input type="text" class="form-control form-control-sm " id="searchInput"
                                        placeholder="Search by name or email..." style="border-left: none;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted mb-1 small">
                                    <i class="ph ph-check-circle me-1"></i>Status Filter
                                </label>
                                <select class="form-select form-select-sm py-1" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
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

        <!-- Employees Table Section -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 py-3" style="margin-top: 20px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ph ph-list me-2 text-primary"></i>Employee List
                            </h6>
                            <span class="badge bg-primary px-3 py-2">
                                <i class="ph ph-users me-1"></i>{{ $employees->total() }} Employees
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="employeesTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-3 py-3 fw-semibold text-dark text-center"
                                            style="width: 60px;">
                                            <i class="ph ph-hash me-1 text-muted"></i>#
                                        </th>
                                        <th class="border-0 px-3 py-3 fw-semibold text-dark">
                                            <i class="ph ph-user me-2 text-muted"></i>Employee
                                        </th>
                                        <th class="border-0 px-3 py-3 fw-semibold text-dark">
                                            <i class="ph ph-envelope me-2 text-muted"></i>Contact
                                        </th>
                                        <th class="border-0 px-3 py-3 fw-semibold text-dark">
                                            <i class="ph ph-briefcase me-2 text-muted"></i>Position
                                        </th>
                                        <th class="border-0 px-3 py-3 fw-semibold text-dark">
                                            <i class="ph ph-check-circle me-2 text-muted"></i>Status
                                        </th>
                                        <th class="border-0 px-3 py-3 fw-semibold text-dark text-center">
                                            <i class="ph ph-gear me-2 text-muted"></i>Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $index => $employee)
                                        <tr class="border-bottom">
                                            <td class="px-3 py-3 text-center">
                                                <span class="badge bg-light text-dark px-2 py-1 fw-semibold">
                                                    {{ $employees->firstItem() + $index }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="d-flex align-items-center">

                                                    <div>
                                                        <h6 class="mb-1 fw-semibold text-dark fs-6">{{ $employee->name }}
                                                        </h6>
                                                        <small class="text-muted">{{ $employee->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ph ph-envelope me-2 text-muted"></i>
                                                        <small class="text-dark">{{ $employee->email }}</small>
                                                    </div>
                                                    @if ($employee->phone)
                                                        <div class="d-flex align-items-center">
                                                            <i class="ph ph-phone me-2 text-muted"></i>
                                                            <small class="text-dark">{{ $employee->phone }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-3 py-3">
                                                @if ($employee->position)
                                                    <span class="badge bg-light text-dark px-3 py-2 border">
                                                        <i class="ph ph-briefcase me-1"></i>{{ $employee->position }}
                                                    </span>
                                                @else
                                                    <span class="text-muted fst-italic">Not specified</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3">
                                                <span
                                                    class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} px-3 py-2">
                                                    <i
                                                        class="ph ph-{{ $employee->status === 'active' ? 'check-circle' : 'pause-circle' }} me-1"></i>
                                                    {{ ucfirst($employee->status) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <div class="btn-group btn-group-sm shadow-sm" role="group">
                                                    <!-- <a href="{{ route('employees.show', $employee->id) }}"
                                                       class="btn btn-info btn-sm border"
                                                       title="View Details"
                                                       style="min-width: 40px;">
                                                        <i class="ph ph-eye"></i>
                                                    </a> -->
                                                    <a href="{{ route('employees.edit', $employee->id) }}"
                                                        class="btn btn-primary btn-sm border" title="Edit Employee"
                                                        style="min-width: 20px;">
                                                        <i class="ph ph-pencil"></i>
                                                    </a>
                                                    <!-- <a href="{{ route('employees.change-password', $employee->id) }}"
                                                       class="btn btn-warning btn-sm border"
                                                       title="Change Password"
                                                       style="min-width: 20px;">
                                                        <i class="ph ph-key"></i>
                                                    </a> -->
                                                    <button type="button"
                                                        class="btn btn-{{ $employee->status === 'active' ? 'warning' : 'success' }} btn-sm border"
                                                        onclick="toggleStatus('{{ $employee->id }}')"
                                                        title="{{ $employee->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                                        style="min-width: 20px;">
                                                        <i
                                                            class="ph ph-{{ $employee->status === 'active' ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm border"
                                                        onclick="deleteEmployee('{{ $employee->id }}')"
                                                        title="Delete Employee" style="min-width: 20px;">
                                                        <i class="ph ph-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                                                        style="width: 80px; height: 80px;">
                                                        <i class="ph ph-users text-muted" style="font-size: 2rem;"></i>
                                                    </div>
                                                    <h5 class="text-muted mb-2">No employees found</h5>
                                                    <p class="text-muted mb-3">Get started by adding your first team member
                                                    </p>
                                                    <a href="{{ route('employees.create') }}"
                                                        class="btn btn-primary btn-lg shadow-sm">
                                                        <i class="ph ph-plus-circle me-2"></i>Add First Employee
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
        @if ($employees->hasPages())
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of
                                    {{ $employees->total() }} entries
                                </div>
                                <div>
                                    {{ $employees->links() }}
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
                    <p class="text-center text-muted mb-0">This action cannot be undone. The employee will be permanently
                        removed from the system.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="ph ph-x me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmDelete">
                        <i class="ph ph-trash me-2"></i>Delete Employee
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let employeeToDelete = null;

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

            $('#employeesTable tbody tr').each(function() {
                const employeeName = $(this).find('td:nth-child(2) h6').text().toLowerCase();
                const employeeEmail = $(this).find('td:nth-child(2) small').text().toLowerCase();
                const statusBadge = $(this).find('td:nth-child(5) .badge');
                const status = statusBadge.text().toLowerCase().includes('active') ? 'active' : 'inactive';

                // Check if row matches search term
                const matchesSearch = !searchTerm ||
                    employeeName.includes(searchTerm) ||
                    employeeEmail.includes(searchTerm);

                // Check if row matches status filter
                const matchesStatus = !selectedStatus || status === selectedStatus;

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
            $('#employeesTable tbody tr').show();
        });

        function deleteEmployee(employeeId) {
            employeeToDelete = employeeId;
            $('#deleteModal').modal('show');
        }

        function toggleStatus(employeeId) {
            $.ajax({
                url: `/admin/employees/${employeeId}/toggle-status`,
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

        $('#confirmDelete').click(function() {
            if (employeeToDelete) {
                $.ajax({
                    url: `/admin/employees/${employeeToDelete}`,
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
