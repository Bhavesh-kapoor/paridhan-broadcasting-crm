@extends('layouts.app_layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('/assets/css/enhanced-tables.css') }}">
@endsection
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Conversations</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Conversations</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            
            <!--table wrapper -->
            <div class="card mb-0 border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bx bx-conversation me-2 text-primary"></i>Conversation Management
                        </h6>
                        <a href="{{ route('conversations.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Add New Conversation
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table id="conversations_table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr class="table-header-gradient">
                                    <th class="text-center"><i class="bx bx-hash me-1"></i>Sl.No</th>
                                    <th><i class="bx bx-building me-1"></i>Exhibitor</th>
                                    <th><i class="bx bx-user me-1"></i>Visitor</th>
                                    <th><i class="bx bx-user-circle me-1"></i>Employee</th>
                                    <th><i class="bx bx-map me-1"></i>Location</th>
                                    <th><i class="bx bx-table me-1"></i>Table</th>
                                    <th><i class="bx bx-megaphone me-1"></i>Campaign</th>
                                    <th class="text-center"><i class="bx bx-info-circle me-1"></i>Outcome</th>
                                    <th><i class="bx bx-calendar me-1"></i>Date</th>
                                    <th class="text-center"><i class="bx bx-cog me-1"></i>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!--end table wrapper-->
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const conversationsTable = $('#conversations_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollX: true,
                scrollCollapse: true,
                pagination: true,
                ajax: {
                    url: `{{ route('conversations.list') }}`,
                    type: 'POST',
                    data: function(d) {
                        d.status = '{{ $status ?? '' }}';
                    }
                },
                columns: [{
                        data: null,
                        name: 'id',
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                    },
                    { data: 'exhibitor_name', name: 'exhibitor_name' },
                    { data: 'visitor_name', name: 'visitor_name' },
                    { data: 'employee_name', name: 'employee_name' },
                    { data: 'location_name', name: 'location_name' },
                    { data: 'table_name', name: 'table_name' },
                    { data: 'campaign_name', name: 'campaign_name' },
                    { data: 'outcome_badge', name: 'outcome', className: "text-center" },
                    { data: 'conversation_date', name: 'conversation_date' },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                ],
                "columnDefs": [{
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    "orderable": false,
                    "sorting": false
                }],
            });

            // Delete conversation
            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                if (id) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Are you sure?',
                        text: 'You want to delete this conversation?',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#555',
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: `${base_url}/admin/conversations/${id}`,
                                type: 'DELETE',
                                success: function(response) {
                                    if (response.status == true) {
                                        toastr.success(response.message);
                                        conversationsTable.ajax.reload();
                                    } else {
                                        toastr.error(response.message);
                                    }
                                },
                                error: function() {
                                    toastr.error('Server error. Please try again.');
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
