@extends('layouts.app_layout')

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">WhatsApp Templates</div>
                <div class="ps-3">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route(Auth::user()->role . '.dashboard')}}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                        <li class="breadcrumb-item active">Edit Template</li>
                    </ol>
                </div>

                <div class="ms-auto">
                    <a href="{{ route('templates.index') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="bx bx-list-ul"></i>&nbsp;All Templates
                    </a>
                </div>
            </div>

            <!-- Edit Template Info -->
            <div class="row mt-4">
                <div class="col-lg-8 mx-auto">
                    <div class="alert alert-warning" role="alert">
                        <i class="bx bx-info-circle"></i>
                        <strong>Note:</strong> Meta/WhatsApp typically does not allow editing approved templates.
                        To make changes, you may need to delete this template and create a new one.
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6 class="fw-semibold text-dark d-flex align-items-center mb-0 py-2">
                                <i class="bx bx-message-square-edit text-primary me-2"></i>Template Details
                            </h6>
                        </div>

                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Template ID</th>
                                    <td>{{ $template['id'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Template Name</th>
                                    <td><strong>{{ $template['name'] ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Language</th>
                                    <td>{{ strtoupper($template['language'] ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>
                                        <span class="badge {{ ($template['category'] ?? '') === 'MARKETING' ? 'bg-primary' : 'bg-success' }}">
                                            {{ $template['category'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge {{ ($template['status'] ?? '') === 'APPROVED' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $template['status'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            @if(isset($template['components']) && count($template['components']) > 0)
                                <div class="mt-4">
                                    <h6 class="fw-semibold">Template Components:</h6>
                                    @foreach($template['components'] as $index => $component)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <h6 class="text-primary">{{ strtoupper($component['type'] ?? 'UNKNOWN') }}</h6>
                                                <pre class="mb-0" style="background: #f5f5f5; padding: 10px; border-radius: 5px;">{{ json_encode($component, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card mt-3">
                        <div class="card-body d-flex justify-content-between">
                            <a href="{{ route('templates.index') }}" class="btn btn-secondary d-flex align-items-center">
                                <i class="bx bx-arrow-back"></i>&nbsp;Back to Templates
                            </a>

                            <button type="button" class="btn btn-danger d-flex align-items-center" id="deleteBtn" data-id="{{ $template['name'] ?? '' }}">
                                <i class="bx bx-trash"></i>&nbsp;Delete Template
                            </button>
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
            // Delete button handler
            $('#deleteBtn').on('click', function() {
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
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status === true) {
                                    toastr.success(response.message);
                                    setTimeout(function() {
                                        window.location.href = `${base_url}/admin/templates`;
                                    }, 1500);
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
    </script>
@endsection
