@extends('layouts.app_layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('/assets/css/enhanced-tables.css') }}">
@endsection
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
                            <li class="breadcrumb-item active" aria-current="page">All Templates</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('templates.create') }}" class="btn btn-success me-2">
                        <i class="bx bx-plus-circle"></i> Create Template
                    </a>
                    <button type="button" class="btn btn-primary" id="refreshBtn">
                        <i class="bx bx-refresh"></i> Refresh Templates
                    </button>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Status Filter -->
            <div class="card mb-3">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <label class="form-label mb-0 me-2"><strong>Filter by Status:</strong></label>
                            <select class="form-select form-select-sm d-inline-block" id="statusFilter" style="width: auto;">
                                <option value="">All Statuses</option>
                                <option value="APPROVED">Approved</option>
                                <option value="PENDING">Pending</option>
                                <option value="REJECTED">Rejected</option>
                                <option value="PENDING_DELETION">Pending Deletion</option>
                                <option value="LIMITED">Limited</option>
                                <option value="PAUSED">Paused</option>
                            </select>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted" id="templateCount">
                                <i class="bx bx-info-circle"></i> <span id="countText">Loading...</span>
                            </small>
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
                                    <th class="text-center"><i class="bx bx-hash"></i> Sl.No</th>
                                    <th><i class="bx bx-file-blank"></i> Template Name</th>
                                    <th class="text-center"><i class="bx bx-globe"></i> Language</th>
                                    <th class="text-center"><i class="bx bx-category"></i> Category</th>
                                    <th class="text-center"><i class="bx bx-info-circle"></i> Status</th>
                                    <th class="text-center"><i class="bx bx-calendar"></i> Created Date</th>
                                    <th class="text-center"><i class="bx bx-cog"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end table wrapper-->
        </div>
    </div>

    <!-- Template Preview Modal -->
    <div class="modal fade" id="templatePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Template Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="templatePreviewContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let dataTableList;

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            dataTableList = $('#data_table').DataTable({
                processing: true,
                serverSide: false,
                autoWidth: true,
                scrollX: true,
                scrollCollapse: true,
                pagination: true,
                deferRender: true,
                ajax: {
                    url: `${base_url}/admin/templates-fetch`,
                    type: 'GET',
                    dataSrc: function(json) {
                        if (json.success) {
                            // Update template count
                            const totalTemplates = json.data.length;
                            const approved = json.data.filter(t => t.status === 'APPROVED').length;
                            const pending = json.data.filter(t => t.status === 'PENDING').length;
                            const rejected = json.data.filter(t => t.status === 'REJECTED').length;
                            
                            $('#countText').html(
                                `Total: <strong>${totalTemplates}</strong> | ` +
                                `Approved: <span class="text-success">${approved}</span> | ` +
                                `Pending: <span class="text-warning">${pending}</span> | ` +
                                `Rejected: <span class="text-danger">${rejected}</span>`
                            );
                            
                            // Initialize tooltips after data is loaded
                            setTimeout(() => {
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl);
                                });
                            }, 100);
                            
                            return json.data;
                        } else {
                            toastr.error(json.message || 'Failed to load templates');
                            $('#countText').text('Error loading templates');
                            return [];
                        }
                    },
                    error: function(xhr, error, thrown) {
                        toastr.error('Failed to fetch templates from Meta API');
                        $('#countText').text('Error loading templates');
                        console.error('DataTable error:', error, thrown);
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'id',
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return `<strong>${data}</strong>`;
                        }
                    },
                    {
                        data: 'language',
                        name: 'language',
                        className: "text-center",
                        render: function(data, type, row) {
                            return data.toUpperCase();
                        }
                    },
                    {
                        data: 'category',
                        name: 'category',
                        render: function(data, type, row) {
                            let badgeClass = 'bg-info';
                            if (data === 'MARKETING') badgeClass = 'bg-primary';
                            if (data === 'UTILITY') badgeClass = 'bg-success';
                            if (data === 'AUTHENTICATION') badgeClass = 'bg-warning';

                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            let badgeClass = 'bg-secondary';
                            let icon = '';
                            let tooltip = '';
                            
                            switch(data) {
                                case 'APPROVED':
                                    badgeClass = 'bg-success';
                                    icon = '<i class="bx bx-check-circle me-1"></i>';
                                    tooltip = 'Template is approved and ready to use';
                                    break;
                                case 'PENDING':
                                    badgeClass = 'bg-warning text-dark';
                                    icon = '<i class="bx bx-time-five me-1"></i>';
                                    tooltip = 'Template is pending review by Meta';
                                    break;
                                case 'REJECTED':
                                    badgeClass = 'bg-danger';
                                    icon = '<i class="bx bx-x-circle me-1"></i>';
                                    tooltip = row.rejection_reason ? `Rejected: ${row.rejection_reason}` : 'Template was rejected';
                                    break;
                                case 'PENDING_DELETION':
                                    badgeClass = 'bg-info';
                                    icon = '<i class="bx bx-trash me-1"></i>';
                                    tooltip = 'Template is pending deletion';
                                    break;
                                case 'LIMITED':
                                    badgeClass = 'bg-warning text-dark';
                                    icon = '<i class="bx bx-error-circle me-1"></i>';
                                    tooltip = 'Template has limited availability';
                                    break;
                                case 'PAUSED':
                                    badgeClass = 'bg-secondary';
                                    icon = '<i class="bx bx-pause-circle me-1"></i>';
                                    tooltip = 'Template is paused';
                                    break;
                                default:
                                    badgeClass = 'bg-secondary';
                                    icon = '<i class="bx bx-help-circle me-1"></i>';
                                    tooltip = 'Unknown status';
                            }

                            return `<span class="badge ${badgeClass}" data-bs-toggle="tooltip" title="${tooltip}">${icon}${data}</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        name: 'action',
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-action btn-view viewTemplateBtn" data-id="${row.id}" data-template='${JSON.stringify(row)}' data-bs-toggle="tooltip" data-bs-placement="top" title="Preview Template">
                                        <i class="bx bx-show"></i>
                                        <span class="d-none d-md-inline">Preview</span>
                                    </button>
                                    ${row.status === 'APPROVED' ? '' : `
                                    <button type="button" class="btn btn-action btn-edit editBtn" data-id="${row.id}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Template">
                                        <i class="bx bx-edit"></i>
                                        <span class="d-none d-md-inline">Edit</span>
                                    </button>
                                    `}
                                    ${row.status !== 'DELETED' ? `
                                    <button type="button" class="btn btn-action btn-delete deleteBtn" data-id="${row.name}" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Template">
                                        <i class="bx bx-trash"></i>
                                        <span class="d-none d-md-inline">Delete</span>
                                    </button>
                                    ` : ''}
                                </div>
                            `;
                        }
                    }
                ],
                dom: "<'row'<'col-12 col-md-6'l><'col-12 col-md-6'f>>" +
                     "<'row'<'col-12'tr>>" +
                     "<'row'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
                language: {
                    emptyTable: "No templates found. Please check your Meta API configuration.",
                    loadingRecords: "Loading templates from Meta API...",
                    processing: "Processing...",
                    search: "Search templates:"
                },
                initComplete: function(settings, json) {
                    // Re-initialize tooltips after table is fully loaded
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            });

            // Status filter functionality
            $('#statusFilter').on('change', function() {
                const status = $(this).val();
                if (status === '') {
                    dataTableList.column(4).search('').draw();
                } else {
                    dataTableList.column(4).search('^' + status + '$', true, false).draw();
                }
            });

            // Refresh button handler
            $('#refreshBtn').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true);
                btn.html('<i class="bx bx-loader-alt bx-spin"></i> Refreshing...');

                $.ajax({
                    url: `${base_url}/admin/templates-refresh`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || 'Templates refreshed successfully');
                            dataTableList.ajax.reload();
                        } else {
                            toastr.error(response.message || 'Failed to refresh templates');
                        }
                    },
                    error: function(xhr, error, thrown) {
                        toastr.error('Failed to refresh templates');
                        console.error('Refresh error:', error, thrown);
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                        btn.html('<i class="bx bx-refresh"></i> Refresh Templates');
                    }
                });
            });

            // View template button handler
            $(document).on('click', '.viewTemplateBtn', function() {
                const templateData = $(this).data('template');
                showTemplatePreview(templateData);
            });

            // Edit template button handler
            $(document).on('click', '.editBtn', function() {
                const templateId = $(this).data('id');
                window.location.href = `${base_url}/admin/templates/${templateId}/edit`;
            });

            // Delete template button handler
            $(document).on('click', '.deleteBtn', function() {
                const templateName = $(this).data('id');

                Swal.fire({
                    icon: 'warning',
                    title: 'Delete Template?',
                    text: 'Are you sure you want to delete this template? This action cannot be undone.',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${base_url}/admin/templates/${templateName}`,
                            type: 'DELETE',
                            success: function(response) {
                                if (response.status === true) {
                                    toastr.success(response.message);
                                    dataTableList.ajax.reload();
                                } else {
                                    toastr.error(response.message || 'Failed to delete template');
                                }
                            },
                            error: function(xhr, error, thrown) {
                                toastr.error('Failed to delete template');
                                console.error('Delete error:', error, thrown);
                            }
                        });
                    }
                });
            });
        });

        function showTemplatePreview(template) {
            const modal = new bootstrap.Modal(document.getElementById('templatePreviewModal'));

            let componentsHtml = '';
            if (template.components && template.components.length > 0) {
                componentsHtml = '<div class="mt-3"><h6>Template Components:</h6>';
                template.components.forEach((component, index) => {
                    componentsHtml += `
                        <div class="card mb-2">
                            <div class="card-body">
                                <h6 class="text-primary">${component.type.toUpperCase()}</h6>
                                <pre class="mb-0" style="background: #f5f5f5; padding: 10px; border-radius: 5px;">${JSON.stringify(component, null, 2)}</pre>
                            </div>
                        </div>
                    `;
                });
                componentsHtml += '</div>';
            }

            const content = `
                <div class="template-details">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Template ID</th>
                            <td>${template.id}</td>
                        </tr>
                        <tr>
                            <th>Template Name</th>
                            <td><strong>${template.name}</strong></td>
                        </tr>
                        <tr>
                            <th>Language</th>
                            <td>${template.language.toUpperCase()}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>
                                <span class="badge ${template.category === 'MARKETING' ? 'bg-primary' : template.category === 'UTILITY' ? 'bg-success' : 'bg-warning text-dark'}">
                                    ${template.category}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge ${template.status === 'APPROVED' ? 'bg-success' : template.status === 'PENDING' ? 'bg-warning text-dark' : template.status === 'REJECTED' ? 'bg-danger' : template.status === 'PENDING_DELETION' ? 'bg-info' : 'bg-secondary'}">
                                    ${template.status === 'APPROVED' ? '<i class="bx bx-check-circle me-1"></i>' : template.status === 'PENDING' ? '<i class="bx bx-time-five me-1"></i>' : template.status === 'REJECTED' ? '<i class="bx bx-x-circle me-1"></i>' : ''}
                                    ${template.status}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created Date</th>
                            <td>${template.created_at || 'N/A'}</td>
                        </tr>
                    </table>
                    ${template.status === 'REJECTED' && template.rejection_reason ? `
                    <div class="alert alert-danger mt-3">
                        <h6 class="alert-heading"><i class="bx bx-error-circle me-2"></i>Rejection Reason:</h6>
                        <p class="mb-0">${template.rejection_reason}</p>
                    </div>
                    ` : ''}
                    ${componentsHtml}
                </div>
            `;

            $('#templatePreviewContent').html(content);
            modal.show();
        }
    </script>
@endsection
