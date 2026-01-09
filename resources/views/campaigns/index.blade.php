@extends('layouts.app_layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('/assets/css/enhanced-tables.css') }}">
    <style>
        /* Campaigns table specific styling */
        #data_table {
            margin-top: 0 !important;
        }
        
        #data_table thead th {
            padding: 10px 8px !important;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
        }
        
        #data_table tbody td {
            padding: 6px 8px !important;
            font-size: 0.85rem;
            vertical-align: middle;
        }
        
        #data_table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }
        
        #data_table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Responsive table wrapper */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            #data_table {
                font-size: 0.8rem;
            }
            
            #data_table thead th,
            #data_table tbody td {
                padding: 5px 6px !important;
            }
            
            #data_table .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
        
        /* Compact action buttons */
        #data_table .btn-action {
            padding: 0.25rem 0.5rem;
            margin: 0 1px;
            font-size: 0.8rem;
            min-width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        #data_table .btn-action i {
            font-size: 0.9rem;
        }
        
        /* Compact badges in table */
        #data_table .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Better spacing for DataTables controls */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin: 10px 0;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.3rem 0.6rem;
            margin: 0 2px;
        }
        
        /* Ensure revenue field text is always black */
        #data_table tbody td .badge.bg-gradient-primary {
            color: #000000 !important;
        }
        
        #data_table .revenue-cell {
            color: #000000 !important;
        }
        
        #data_table .revenue-cell * {
            color: #000000 !important;
        }
    </style>
@endsection
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Campaign Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role . '.dashboard')}}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Campaigns</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <!--table wrapper -->
            <div class="card mb-0 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-megaphone me-2"></i>Campaigns List
                        </h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="data_table" class="table table-hover mb-0" style="width:100%">
                            <thead class="table-header-gradient">
                                <tr>
                                    <th class="text-center" style="width: 60px;"><i class="bx bx-hash"></i> #</th>
                                    <th><i class="bx bx-rename"></i> Campaign Name</th>
                                    <th><i class="bx bx-text"></i> Subject</th>
                                    <th class="text-center"><i class="bx bx-send"></i> Sent</th>
                                    <th class="text-center"><i class="bx bx-user-plus"></i> Leads</th>
                                    <th class="text-center"><i class="bx bx-check-circle"></i> Bookings</th>
                                    <th class="text-end"><i class="bx bx-rupee"></i> Revenue</th>
                                    <th class="text-center"><i class="bx bx-category"></i> Type</th>
                                    <th class="text-center"><i class="bx bx-info-circle"></i> Status</th>
                                    <th class="text-center"><i class="bx bx-calendar"></i> Created</th>
                                    <th class="text-center" style="width: 150px;"><i class="bx bx-cog"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end table wrapper-->
        </div>
    </div>

    <div class="modal fade" id="progressModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sending Campaign...</h5>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar" id="modalUploadProgressBar" style="width:0%">0%</div>
                    </div>
                    <div class="mt-2 text-center" id="modalProgressText">Sent: 0 / 0 | Failed: 0 | Pending: 0</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let campaignId;
        // $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const dataTableList = $('#data_table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            scrollX: false,
            scrollCollapse: false,
            responsive: true,
            pagination: true,
            pageLength: 25,
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
                    data: 'messages_sent',
                    name: 'messages_sent',
                    className: "text-center",
                    orderable: false
                },
                {
                    data: 'leads_generated',
                    name: 'leads_generated',
                    className: "text-center",
                    orderable: false
                },
                {
                    data: 'bookings_created',
                    name: 'bookings_created',
                    className: "text-center",
                    orderable: false
                },
                {
                    data: 'revenue',
                    name: 'revenue',
                    className: "text-end",
                    orderable: false,
                    render: function(data, type, row) {
                        return '<div style="color: #000000 !important;">' + data + '</div>';
                    }
                },
                {
                    data: 'full_type',
                    name: 'full_type',
                    className: "text-center"
                },
                {
                    data: 'full_status',
                    name: 'full_status',
                    className: "text-center"
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: "text-center"
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
                "targets": [1, 2, 3, 4, 5, 6, 7, 8, 9],
                "orderable": false,
                "sorting": false
            }],
            dom: "<'row g-2 mb-2'<'col-12 col-md-4'B><'col-12 col-md-4'l><'col-12 col-md-4'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row g-2 mt-2'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
            language: {
                emptyTable: "No campaigns found",
                zeroRecords: "No matching campaigns found"
            },
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
                                // // Show modal
                                // campaignId = id; // set current campaign ID
                                // let progressModal = new bootstrap.Modal(document
                                //     .getElementById('progressModal'));
                                // progressModal.show();
                                toastr.success(response.message);
                                dataTableList.ajax.reload();

                                // campaignId = id; // set current campaign ID
                                // let progressModal = new bootstrap.Modal(document.getElementById(
                                //     'progressModal'));
                                // progressModal.show();
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
        // });

        function createCampaign() {
            window.location.href = `{{ route('campaigns.create') }}`;
        }





        // let maxTime = 50; // in seconds
        // let elapsed = 0;

        // let interval = setInterval(() => {
        //     if (!campaignId) return;

        //     $.ajax({
        //         url: base_url + `/admin/campaigns/${campaignId}/progress`,
        //         type: 'GET',
        //         global: false,
        //         success: function(res) {
        //             // Update progress bar and text
        //             $('#modalUploadProgressBar').css('width', res.percent + '%').text(res.percent +
        //             '%');
        //             $('#modalProgressText').text(
        //                 `Sent: ${res.sent} / ${res.total} | Failed: ${res.failed} | Pending: ${res.pending}`
        //             );

        //             // Stop if all processed
        //             if (res.sent + res.failed >= res.total) {
        //                 clearInterval(interval);
        //                 progressModal.hide();
        //                 alert('All recipients processed!');
        //             }
        //         },
        //         error: function(errors) {
        //             console.log("Server error! Please try again.");
        //         }
        //     });

        //     elapsed++;
        //     if (elapsed >= maxTime) { // stop after 50 seconds
        //         clearInterval(interval);
        //         console.log("Stopped polling after 50 seconds.");
        //     }
        // }, 5000); // polling every 1 second
    </script>
@endsection
