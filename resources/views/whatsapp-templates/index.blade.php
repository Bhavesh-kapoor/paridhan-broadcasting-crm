@extends('layouts.app_layout')
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">WhatsApp Templates</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role . '.dashboard')}}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle ?? 'All Templates' }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="bx bx-message-detail me-2"></i>{{ $pageTitle ?? 'WhatsApp Message Templates' }}
                                        @if($status)
                                            <span class="badge 
                                                @if($status === 'APPROVED') bg-success
                                                @elseif($status === 'PENDING') bg-warning
                                                @elseif($status === 'REJECTED') bg-danger
                                                @elseif($status === 'PAUSED') bg-secondary
                                                @endif ms-2">{{ $status }}</span>
                                        @endif
                                    </h5>
                                    <p class="text-muted mb-0 small">
                                        @if($status === 'APPROVED')
                                            Templates approved and ready to use in campaigns
                                        @elseif($status === 'PENDING')
                                            Templates awaiting approval from Meta
                                        @elseif($status === 'REJECTED')
                                            Templates rejected by Meta
                                        @elseif($status === 'PAUSED')
                                            Templates temporarily paused
                                        @else
                                            Manage and sync templates from WhatsApp API
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary" id="syncTemplatesBtn">
                                        <i class="bx bx-refresh me-1"></i>Sync from API
                                    </button>
                                    @if($status)
                                        <a href="{{ route('whatsapp-templates.index') }}" class="btn btn-outline-secondary ms-2">
                                            <i class="bx bx-list-ul me-1"></i>View All
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Search</label>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search templates...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="APPROVED" {{ $status === 'APPROVED' ? 'selected' : '' }}>Approved</option>
                                        <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>Pending</option>
                                        <option value="REJECTED" {{ $status === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                                        <option value="PAUSED" {{ $status === 'PAUSED' ? 'selected' : '' }}>Paused</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" id="categoryFilter">
                                        <option value="">All Categories</option>
                                        <option value="MARKETING">Marketing</option>
                                        <option value="UTILITY">Utility</option>
                                        <option value="AUTHENTICATION">Authentication</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-secondary w-100" id="resetFiltersBtn">
                                        <i class="bx bx-reset me-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--table wrapper -->
            <div class="card mb-0">
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table id="data_table" class="table table-striped table-bordered mt-2" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Template Name / Language</th>
                                    <th>Body Text</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Last Synced</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!--end table wrapper-->
        </div>
    </div>

    <!-- Template Details Modal -->
    <div class="modal fade" id="templateDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Template Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="templateDetailsContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let dataTableList;
        const base_url = "{{ url('/') }}";

        $(document).ready(function() {
            // Initialize DataTable
            dataTableList = $('#data_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: base_url + '/admin/ajax/get/all-whatsapp-templates',
                    type: 'POST',
                    data: function(d) {
                        d.search = $('#searchInput').val();
                        // Use status from URL if filter is empty
                        d.status = $('#statusFilter').val() || '{{ $status ?? "" }}';
                        d.category = $('#categoryFilter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'template_name', name: 'template_name' },
                    { data: 'body_text', name: 'body_text' },
                    { data: 'category_badge', name: 'category' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'synced_at', name: 'synced_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],
                pageLength: 10,
            });

            // Search input
            $('#searchInput').on('keyup', function() {
                dataTableList.draw();
            });

            // Filter dropdowns
            $('#statusFilter, #categoryFilter').on('change', function() {
                dataTableList.draw();
            });

            // Reset filters
            $('#resetFiltersBtn').on('click', function() {
                $('#searchInput').val('');
                $('#statusFilter').val('');
                $('#categoryFilter').val('');
                dataTableList.draw();
            });

            // Sync templates
            $('#syncTemplatesBtn').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Syncing...');

                $.ajax({
                    url: base_url + '/admin/whatsapp-templates/sync',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === true) {
                            let message = response.message || 'Templates synced successfully!';
                            
                            // Show detailed statistics if available
                            if (response.stats) {
                                const stats = response.stats;
                                message += `\n\nApproved: ${stats.approved || 0} | Pending: ${stats.pending || 0} | Rejected: ${stats.rejected || 0} | Paused: ${stats.paused || 0}`;
                            }
                            
                            toastr.success(message, 'Sync Complete', {
                                timeOut: 5000,
                                extendedTimeOut: 3000
                            });
                            dataTableList.ajax.reload();
                        } else {
                            toastr.error(response.message || 'Failed to sync templates');
                        }
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.message || 'Failed to sync templates';
                        toastr.error(error);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('<i class="bx bx-refresh me-1"></i>Sync from API');
                    }
                });
            });

            // View template details
            $(document).on('click', '.btnView', function() {
                const templateId = $(this).data('template-id');
                const modal = new bootstrap.Modal(document.getElementById('templateDetailsModal'));
                
                $('#templateDetailsContent').html('<div class="text-center"><div class="spinner-border" role="status"></div></div>');
                modal.show();

                $.ajax({
                    url: base_url + `/admin/whatsapp-templates/${templateId}/details`,
                    type: 'GET',
                    success: function(response) {
                        if (response.status === true) {
                            const template = response.template;
                            let html = `
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Template Name:</strong><br>
                                        <span class="badge bg-primary">${template.name}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Language:</strong><br>
                                        ${template.language.toUpperCase()}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Category:</strong><br>
                                        <span class="badge bg-info">${template.category}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Status:</strong><br>
                                        <span class="badge ${template.status === 'APPROVED' ? 'bg-success' : 'bg-warning'}">${template.status}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <strong>Body Text:</strong>
                                    <div class="p-3 bg-light rounded">${template.body_text || 'N/A'}</div>
                                </div>
                                <div class="mb-3">
                                    <strong>Last Synced:</strong><br>
                                    ${template.synced_at || 'Never'}
                                </div>
                            `;
                            $('#templateDetailsContent').html(html);
                        } else {
                            $('#templateDetailsContent').html('<div class="alert alert-danger">Failed to load template details</div>');
                        }
                    },
                    error: function() {
                        $('#templateDetailsContent').html('<div class="alert alert-danger">Error loading template details</div>');
                    }
                });
            });

            // Use template in campaign
            $(document).on('click', '.useTemplate', function() {
                const templateName = $(this).data('template-name');
                // Store in sessionStorage to use in campaign creation
                sessionStorage.setItem('selectedTemplate', templateName);
                toastr.success(`Template "${templateName}" selected. Redirecting to campaign creation...`);
                setTimeout(() => {
                    window.location.href = base_url + '/admin/campaigns/create';
                }, 1000);
            });
        });
    </script>
@endsection

