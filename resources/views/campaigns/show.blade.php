@extends('layouts.app_layout')
@section('style')
    <style>
        .countBox {
            padding: 3px 10px 16px 10px;

        }
    </style>
@endsection
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Campaign Management</div>
                <div class="ps-3">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active">Campaign Details</li>
                    </ol>
                </div>

                <div class="ms-auto">
                    <a href="{{ route('campaigns.index') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="bx bxs-megaphone"></i>&nbsp;All Campaigns
                    </a>
                </div>
            </div>

            <!--Show detail-->
            <div class="row">
                <!-- Campaign Information -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark d-flex align-content-center">
                                <i class="bx bxs-megaphone me-2 text-primary"></i>Campaign Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-1">Campaign Name</label>
                                    <p class="mb-3 fw-semibold text-dark">{{ $campaign->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-1">Campaign Type</label>
                                    <p class="mb-3">
                                        <span class="badge bg-light text-dark px-3 py-2 border">
                                            <i class="bx bx-tag me-1"></i>{{ ucfirst($campaign->type) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-1">Subject</label>
                                    <p class="mb-3 fw-semibold text-dark">{{ $campaign->subject }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-1">Status</label>
                                    <p class="mb-3">
                                        <span class="badge {{ $campaign->status_badge_class }} px-3 py-2">
                                            <i
                                                class=" {{ $campaign->status === 'draft' ? 'bx bx-file' : ($campaign->status === 'sent' ? 'bx bx-check-circle' : 'lni lni-alarm-clock') }} me-1"></i>
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-1">Created Date</label>
                                    <p class="mb-3">{{ $campaign->created_at->format('M d, Y \a\t h:i A') }}</p>
                                </div>
                                @if ($campaign->scheduled_at)
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted mb-1">Scheduled Date</label>
                                        <p class="mb-3">{{ $campaign->scheduled_at->format('M d, Y \a\t h:i A') }}</p>
                                    </div>
                                @endif
                                @if ($campaign->sent_at)
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted mb-1">Sent Date</label>
                                        <p class="mb-3">{{ $campaign->sent_at->format('M d, Y \a\t h:i A') }}</p>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-1">Message</label>
                                    <div class="border rounded p-3 bg-light">
                                        <p class="mb-0">{{ $campaign->message }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Statistics -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark d-flex align-content-center">
                                <i class="bx bx-bar-chart-alt me-2 text-primary"></i>Campaign Statistics
                            </h6>
                        </div>
                        <div class="card-body">

                            <div class="row g-3">
                                <!-- Total Recipients -->
                                <div class=" col-6">
                                    <div class="countBox text-center rounded-3" style="background:#e9f2ff;">
                                        <i class="bx bx-group fs-1 text-primary"></i>
                                        <h4 class="fw-bold mb-0 mt-2 text-primary">{{ $campaign->recipient_count }}</h4>
                                        <p class="mb-0 mt-1 fw-semibold text-dark">Total Recipients</p>
                                    </div>
                                </div>

                                <!-- Sent -->
                                <div class=" col-6">
                                    <div class="countBox text-center rounded-3" style="background:#e9f8f3;">
                                        <i class="bx bx-check-circle fs-1 text-success"></i>
                                        <h4 class="fw-bold mb-0 mt-2 text-success">
                                            {{ $campaign->recipients->where('status', 'sent')->count() }}</h4>
                                        <p class="mb-0 mt-1 fw-semibold text-dark">Sent</p>
                                    </div>
                                </div>

                                <!-- Pending -->
                                <div class=" col-6">
                                    <div class="countBox text-center rounded-3" style="background:#fff8e6;">
                                        <i class="bx bx-time-five fs-1 text-warning"></i>
                                        <h4 class="fw-bold mb-0 mt-2 text-warning">
                                            {{ $campaign->recipients->where('status', 'pending')->count() }}</h4>
                                        <p class="mb-0 mt-1 fw-semibold text-dark">Pending</p>
                                    </div>
                                </div>

                                <!-- Failed -->
                                <div class=" col-6">
                                    <div class="countBox text-center rounded-3" style="background:#ffeaea;">
                                        <i class="bx bx-x-circle fs-1 text-danger"></i>
                                        <h4 class="fw-bold mb-0 mt-2 text-danger">
                                            {{ $campaign->recipients->where('status', 'failed')->count() }}</h4>
                                        <p class="mb-0 mt-1 fw-semibold text-dark">Failed</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Campaign Actions -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex  justify-content-between align-content-center">
                                @if ($campaign->isDraft())
                                    <button type="button" class="btn btn-success d-flex  align-content-center sendCampaign"
                                        id="{{ $campaign->id }}" title="Send Campaign">
                                        <i class="bx bx-paper-plane me-2"></i>Send
                                    </button>
                                @endif
                                <a href="{{ route('campaigns.edit', $campaign->id) }}"
                                    class="btn btn-primary d-flex  align-content-center" title="Edit Campaign">
                                    <i class="bx bx-pencil me-2"></i>Edit
                                </a>
                                <button type="button" class="btn btn-danger d-flex  align-content-center deleteBtn"
                                    id="{{ $campaign->id }}" title="Delete Campaign">
                                    <i class="bx bx-trash me-2"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients List -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ph ph-users me-2 text-primary"></i>Campaign Recipients
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="data_table" class="table table-striped table-bordered mt-2"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Sl.No</th>
                                            <th>Name</th>
                                            <th>Contact Info</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
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
                    url: `${base_url}/admin/ajax/get/all-campaigns-recipients`,
                    type: 'POST',
                    global: false,
                    data: function(d) {
                        d.id = `{{ $campaign->id }}`
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
                    {
                        data: 'recipient_name',
                        name: 'recipient_name'
                    },
                    {
                        data: 'recipient_contact',
                        name: 'recipient_contact'
                    },
                    {
                        data: 'recipient_type',
                        name: 'recipient_type'
                    },
                    {
                        data: 'full_status',
                        name: 'full_status'
                    },
                    {
                        data: 'recipient_location',
                        name: 'recipient_location'
                    },
                ],
                "columnDefs": [{
                    "targets": [1, 2, 3, 4, 5],
                    "orderable": false,
                    "sorting": false
                }],

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
                                        Swal.fire({
                                            icon: "success",
                                            title: "Campaign Deleted!",
                                            text: response.message,
                                            confirmButtonText: "OK",
                                            allowOutsideClick: false,
                                            allowEscapeKey: false
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href =
                                                    `{{ route('campaigns.index') }}`;
                                            }
                                        });

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
                                    Swal.fire({
                                        icon: "success",
                                        title: "Campaign Sent!",
                                        html: response.message,
                                        showCancelButton: false,
                                        confirmButtonText: "OK",
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload(true);
                                        }
                                    });
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
    </script>
@endsection
