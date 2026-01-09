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
                            <a href="{{ route(Auth::user()->role . '.dashboard') }}"><i class="bx bx-home-alt"></i></a>
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
                                <!-- Messages Sent -->
                                <div class="col-6">
                                    <div class="countBox text-center rounded-3" style="background:#e9f2ff;">
                                        <i class="bx bx-send fs-1 text-primary"></i>
                                        <h4 class="fw-bold mb-0 mt-2 text-primary">{{ $analytics['total_messages_sent'] ?? 0 }}</h4>
                                        <p class="mb-0 mt-1 fw-semibold text-dark">Messages Sent</p>
                                    </div>
                                </div>

                                <!-- Leads Generated -->
                                <div class="col-6">
                                    <div class="countBox text-center rounded-3" style="background:#fff8e6;">
                                        <i class="bx bx-user-plus fs-1 text-warning"></i>
                                        <h4 class="fw-bold mb-0 mt-2 text-warning">{{ $analytics['total_leads_generated'] ?? 0 }}</h4>
                                        <p class="mb-0 mt-1 fw-semibold text-dark">Leads Generated</p>
                                    </div>
                                </div>

                                <!-- Bookings Created -->
                                <div class="col-6">
                                    <div class="countBox text-center rounded-3" style="background:#e9f8f3;">
                                        <i class="bx bx-check-circle fs-1 text-success"></i>
                                        <h4 class="fw-bold mb-0 mt-2 text-success">{{ $analytics['total_bookings_created'] ?? 0 }}</h4>
                                        <p class="mb-0 mt-1 fw-semibold text-dark">Bookings</p>
                                    </div>
                                </div>

                                <!-- Total Revenue -->
                                <div class="col-6">
                                    <div class="countBox text-center rounded-3" style="background:#f0e9ff;">
                                        <i class="bx bx-rupee fs-1 text-purple" style="color: #764ba2;"></i>
                                        <h4 class="fw-bold mb-0 mt-2" style="color: #764ba2;">₹{{ number_format($analytics['total_revenue'] ?? 0, 2) }}</h4>
                                        <p class="mb-0 mt-1 fw-semibold text-dark">Revenue</p>
                                    </div>
                                </div>

                                <!-- Conversion Rates -->
                                <div class="col-12">
                                    <div class="countBox text-center rounded-3 p-3" style="background:#e8f5e9;">
                                        <i class="bx bx-trending-up fs-2 text-success"></i>
                                        <h3 class="fw-bold mb-0 mt-2 text-success">{{ $analytics['lead_to_booking_conversion'] ?? 0 }}%</h3>
                                        <p class="mb-0 mt-1 fw-semibold text-dark">Lead to Booking</p>
                                        <small class="text-muted">(Bookings / Leads)</small>
                                    </div>
                                </div>
                                
                                @if(isset($analytics['recipient_to_lead_conversion']))
                                <div class="col-6">
                                    <div class="countBox text-center rounded-3 p-2" style="background:#e3f2fd;">
                                        <i class="bx bx-user-check fs-3 text-primary"></i>
                                        <h5 class="fw-bold mb-0 mt-1 text-primary">{{ $analytics['recipient_to_lead_conversion'] ?? 0 }}%</h5>
                                        <small class="text-muted">Recipient → Lead</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="countBox text-center rounded-3 p-2" style="background:#fff3e0;">
                                        <i class="bx bx-check-double fs-3 text-warning"></i>
                                        <h5 class="fw-bold mb-0 mt-1 text-warning">{{ $analytics['recipient_to_booking_conversion'] ?? 0 }}%</h5>
                                        <small class="text-muted">Recipient → Booking</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Actions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="bx bx-conversation me-2 text-primary"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('campaigns.conversations', $campaign->id) }}" class="btn btn-primary">
                                    <i class="bx bx-conversation me-1"></i>View Conversations
                                </a>
                                <a href="{{ route('campaigns.conversations.create', $campaign->id) }}" class="btn btn-success">
                                    <i class="bx bx-plus me-1"></i>Add Conversation
                                </a>
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

            <!-- Revenue Breakdown by Exhibitor -->
            @if(isset($analytics['revenue_by_status']) && ($analytics['total_revenue'] ?? 0) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="bx bx-rupee me-2 text-success"></i>Revenue Breakdown
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="text-center p-3 rounded" style="background: #e9f8f3;">
                                        <h5 class="fw-bold text-success mb-1">₹{{ number_format($analytics['revenue_by_status']['paid'] ?? 0, 2) }}</h5>
                                        <small class="text-muted">Paid</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 rounded" style="background: #fff8e6;">
                                        <h5 class="fw-bold text-warning mb-1">₹{{ number_format($analytics['revenue_by_status']['partial'] ?? 0, 2) }}</h5>
                                        <small class="text-muted">Partial</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 rounded" style="background: #ffeaea;">
                                        <h5 class="fw-bold text-danger mb-1">₹{{ number_format($analytics['revenue_by_status']['unpaid'] ?? 0, 2) }}</h5>
                                        <small class="text-muted">Unpaid</small>
                                    </div>
                                </div>
                            </div>
                            @if(isset($revenueByExhibitor) && count($revenueByExhibitor) > 0)
                            <h6 class="fw-semibold mb-3">Revenue by Exhibitor:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Exhibitor Name</th>
                                            <th class="text-center">Bookings</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($revenueByExhibitor as $exhibitor)
                                        <tr>
                                            <td>{{ $exhibitor->exhibitor_name }}</td>
                                            <td class="text-center">{{ $exhibitor->bookings_count }}</td>
                                            <td class="text-end fw-bold text-success">₹{{ number_format($exhibitor->total_revenue, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recipients List -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="bx bx-users me-2 text-primary"></i>Campaign Recipients
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="data_table" class="table table-striped table-bordered mt-2"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><i class="bx bx-hash"></i> Sl.No</th>
                                            <th><i class="bx bx-user"></i> Name</th>
                                            <th><i class="bx bx-phone"></i> Contact Info</th>
                                            <th class="text-center"><i class="bx bx-category"></i> Type</th>
                                            <th class="text-center"><i class="bx bx-info-circle"></i> Message Status</th>
                                            <th class="text-center"><i class="bx bx-conversation"></i> Conversation</th>
                                            <th class="text-center"><i class="bx bx-check-circle"></i> Booking</th>
                                            <th class="text-center"><i class="bx bx-rupee"></i> Revenue</th>
                                            <th><i class="bx bx-map"></i> Location</th>
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
                        name: 'recipient_type',
                        className: "text-center"
                    },
                    {
                        data: 'full_status',
                        name: 'full_status',
                        className: "text-center"
                    },
                    {
                        data: 'conversation_status',
                        name: 'conversation_status',
                        className: "text-center",
                        orderable: false
                    },
                    {
                        data: 'booking_status',
                        name: 'booking_status',
                        className: "text-center",
                        orderable: false
                    },
                    {
                        data: 'revenue',
                        name: 'revenue',
                        className: "text-center",
                        orderable: false
                    },
                    {
                        data: 'recipient_location',
                        name: 'recipient_location'
                    },
                ],
                "columnDefs": [{
                    "targets": [1, 2, 3, 4, 5, 6, 7, 8],
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
