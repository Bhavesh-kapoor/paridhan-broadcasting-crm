@extends('layouts.app_layout')

@section('title', 'System Logs')

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">System Logs</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active">Logs</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="bx bx-file-blank me-2" style="color: var(--sidebar-end, #3b82f6);"></i>Available Log Files
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(count($logFiles) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Log File Name</th>
                                                <th>File Size</th>
                                                <th>Last Modified</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($logFiles as $file)
                                                @php
                                                    $filePath = storage_path('logs/' . $file);
                                                    $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                                    $lastModified = file_exists($filePath) ? filemtime($filePath) : 0;
                                                    $sizeInKB = round($fileSize / 1024, 2);
                                                    $sizeInMB = round($fileSize / (1024 * 1024), 2);
                                                    $displaySize = $sizeInMB >= 1 ? $sizeInMB . ' MB' : $sizeInKB . ' KB';
                                                    $displayDate = $lastModified ? date('M d, Y H:i:s', $lastModified) : 'N/A';
                                                    $routePrefix = Auth::user()->role . '.logs';
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <i class="bx bx-file me-2 text-primary"></i>
                                                        <strong>{{ $file }}</strong>
                                                    </td>
                                                    <td>{{ $displaySize }}</td>
                                                    <td>{{ $displayDate }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route($routePrefix . '.show', $file) }}" class="btn btn-sm btn-primary">
                                                            <i class="bx bx-show me-1"></i>View
                                                        </a>
                                                        <a href="{{ route($routePrefix . '.download', $file) }}" class="btn btn-sm btn-info">
                                                            <i class="bx bx-download me-1"></i>Download
                                                        </a>
                                                        @if(Auth::user()->role === 'admin')
                                                        <button type="button" class="btn btn-sm btn-danger clear-log" data-file="{{ $file }}">
                                                            <i class="bx bx-trash me-1"></i>Clear
                                                        </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bx bx-file-blank fs-1 text-muted"></i>
                                    <p class="text-muted mt-3">No log files found</p>
                                </div>
                            @endif
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
        // Clear log file
        $('.clear-log').on('click', function() {
            const file = $(this).data('file');
            
            Swal.fire({
                icon: 'warning',
                title: 'Clear Log File?',
                text: 'Are you sure you want to clear "' + file + '"? This action cannot be undone.',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Clear It',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.logs.clear", ":file") }}'.replace(':file', file),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Cleared!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to clear log file'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to clear log file. Please try again.'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection

