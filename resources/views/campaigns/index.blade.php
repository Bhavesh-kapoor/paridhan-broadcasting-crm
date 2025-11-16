@extends('layouts.app_layout')
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Campaign Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Campaigns</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <!--table wrapper -->
            <div class="card mb-0">
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table id="data_table" class="table table-striped table-bordered mt-2" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>Recipients</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Created</th>
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
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const dataTableList = $('#data_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollX: true,
                scrollCollapse: true,
                pagination: true,
                deferRender: true,
                ajax: {
                    url: `${base_url}/admin/ajax/get/all-campaigns`,
                    type: 'POST',
                    global: false,
                },
                columns: [{
                        data: null,
                        name: 'id',
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    {
                        data: 'recipient_count',
                        name: 'recipient_count'
                    },
                    {
                        data: 'full_type',
                        name: 'full_type'
                    },

                    {
                        data: 'full_status',
                        name: 'full_status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        width: '12%',
                        orderable: false,
                        searchable: false
                    },
                ],
                "columnDefs": [{
                    "targets": [1, 2, 3, 4, 5, 6, 7],
                    "orderable": false,
                    "sorting": false
                }],
                dom: "<'row'<'col-12 col-md-4'B><'col-12 col-md-4'l><'col-12 col-md-4'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
                buttons: [],

            });

            $(window).on('load', function() {
                $('.dataTables_wrapper .dt-buttons').append(
                    `<button type="button" class="btn btn-primary" type="button"  onClick="createCampaign()"><i
                    class="lni lni-circle-plus mx-1"></i>Add New Campaign</button>`
                );
            });
            // handle table action button click
            $(document).on('click', '.editBtn', function() {
                window.location.href = $(this).attr('editRoute');
            });
            $(document).on('click', '.btnView', function() {
                window.location.href = $(this).attr('route');
            });
            $(document).on('click', '.deleteBtn', function() {
                var id = $(this).attr('id');
                if (id) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Are you sure?',
                        text: 'You want to delete this record?',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#555',
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {

                        /* Read more about isConfirmed, isDenied below */
                        if (result.value) {
                            $.ajax({
                                url: base_url + `/admin/campaigns/${id}`,
                                type: 'DELETE',
                                success: function(response) {
                                    if (response.status == true) {
                                        toastr.success(response.message);
                                        dataTableList.ajax.reload();
                                    } else if (response.status == false) {
                                        toastr.error(response.message);
                                    } else {
                                        toastr.error(response.message);
                                    }
                                },
                                error: function(errors) {
                                    toastr.error(error)
                                }
                            });
                        }

                    });
                } else {
                    toastr.error('Something went wrong. Please try again.');
                }

            });
            $(document).on('click', '.sendCampaign', function() {
                var id = $(this).attr('id');

                if (!id) {
                    toastr.error('Something went wrong. Please try again.');
                    return;
                }

                Swal.fire({
                    icon: 'question',
                    title: 'Send Campaign?',
                    text: 'Are you sure you want to send this campaign? This action cannot be undone.',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Send Now',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                }).then((result) => {

                    if (result.isConfirmed) {
                        $.ajax({
                            url: base_url + `/admin/campaigns/${id}/send`,
                            type: 'POST',
                            success: function(response) {
                                if (response.status === true) {
                                    toastr.success(response.message);
                                    dataTableList.ajax.reload();
                                } else {
                                    toastr.error(response.message ||
                                        "Something went wrong!");
                                }
                            },
                            error: function(errors) {
                                toastr.error("Server error! Please try again.");
                            }
                        });
                    }

                });
            });
        });

        function createCampaign() {
            window.location.href = `{{ route('campaigns.create') }}`;
        }
    </script>
@endsection
