@extends('layout.master')
@section('title', 'Location Management')

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
                                    <i class="ph ph-map-pin me-3 text-primary"></i>Location Management
                                </h5>
                                <p class="text-muted mb-0 fs-9">Manage your Locations</p>
                            </div>
                            <a href="{{ route('locations.create') }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="ph ph-plus-circle me-2"></i>Create Location
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
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted mb-1 small">
                                    <i class="ph ph-faders-horizontal me-2"></i>Filter
                                </label>
                                <select class="form-select form-select-sm py-1" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="AC">AC</option>
                                    <option value="NON-AC">NON-AC</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- locations Table Section -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm px-5">

                    <div class="card-body px-4">
                        <div class="table-responsive">
                            <table class="table table-hover" id="locationsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Location</th>
                                        <th>Type</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>


@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#locationsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('get.locations.data') }}',
                    data: function(d) {
                        d.status = $('#statusFilter').val(); // send selected filter to backend
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'loc_name',
                        name: 'loc_name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            //  Trigger table reload when filter changes
            $('#statusFilter').on('change', function() {
                table.ajax.reload();
            });

            // View Location Details

            $(document).on('click', '.viewLocation', function() {
                let id = $(this).data('id');
                window.location.href = '/admin/locations/' + id;
            });


            $(document).on('click', '.deleteLocation', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/locations/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    $('#locationsTable').DataTable().ajax
                                        .reload(); // refresh table
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error',
                                    'Something went wrong while deleting.', 'error');
                            }
                        });
                    }
                });
            });







        });
    </script>
@endpush
