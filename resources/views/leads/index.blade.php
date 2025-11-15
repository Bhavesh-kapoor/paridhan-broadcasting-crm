@extends('layout.master')
@section('title', 'lead Management')

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
                                    <i class="ph ph-crown me-3 text-primary"></i>Lead Management
                                </h5>
                                <p class="text-muted mb-0 fs-9">Manage your Leads</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- leads Table Section -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm px-5">

                    <div class="card-body px-4">
                        <div class="table-responsive">
                            <table class="table table-hover" id="leadsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>User Name</th>
                                        <th>Phone</th>
                                        <th>Location</th>
                                        <th>Type</th>
                                        <th class="text-wrap">Crurrent FollowUp Status</th>
                                        <th>Last Status</th>
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


        {{-- offcanvas --}}
        <div class="offcanvas offcanvas-end shadow" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            id="followUpCanvas">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Add Follow-Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body">

                <form id="followUpForm">
                    @csrf
                    <input type="hidden" name="phone" id="phone" readonly>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">-- Select Status --</option>
                            <option value="busy">Busy</option>
                            <option value="interested">Interested</option>
                            <option value="materialised">Materialised</option>
                        </select>
                    </div>

                    <div class="mb-3" id="busyFields" style="display: none;">
                        <label class="form-label">Next Follow-up Date</label>
                        <input type="date" name="next_followup_date" class="form-control"
                            min="@php echo date('Y-m-d'); @endphp">

                        <label class="form-label mt-2">Next Follow-up Time</label>
                        <input type="time" name="next_followup_time" class="form-control"
                            min="@php echo date('H:i'); @endphp">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="comment" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Save Follow-Up</button>

                </form>

            </div>
        </div>

        {{-- view follow-ups offcanvas --}}
        <div class="offcanvas offcanvas-end" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            id="ViewfollowUpCanvas" style="width:600px !important;max-width:80%;">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Follow Up Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body" id="followUpContent">
                <!-- follow-up data will load here -->
            </div>
        </div>




    </div>


@endsection

@push('scripts')
    <script>
        let table = $('#leadsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('get.leads.data') }}',
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
                    data: 'name',
                    name: 'name',
                    className: 'text-center'
                },
                {
                    data: 'phone',
                    name: 'phone',
                    className: 'text-center'
                },
                {
                    data: 'location',
                    name: 'location',
                    className: 'text-center'
                },
                {
                    data: 'type',
                    name: 'type',
                    className: 'text-center'
                },
                {
                    data: 'follow_status_badge',
                    name: 'follow_status_badge',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'follow_up_status',
                    name: 'follow_up_status',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'actions',
                    name: 'actions',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
            ]
        });


        // Open follow-up offcanvas
        $(document).on("click", ".openFollowUp", function() {

            let phone = $(this).data('phone');

            // Set user ID
            $("#phone").val(phone);

            // Reset form
            $("#followUpForm")[0].reset();

            // Hide busy fields initially
            $("#busyFields").hide();

            // Open the offcanvas
            let followCanvas = new bootstrap.Offcanvas(document.getElementById('followUpCanvas'));
            followCanvas.show();
        });

        // Show/hide fields based on status
        $("#status").on("change", function() {
            if ($(this).val() === "busy") {
                $("#busyFields").slideDown();
            } else {
                $("#busyFields").slideUp();
            }
        });




        // AJAX form submit
        $("#followUpForm").on("submit", function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('followup.store') }}",
                type: "POST",
                data: $(this).serialize(),

                success: function(res) {

                    if (res.status) {

                        Swal.fire({
                            title: "Success!",
                            text: res.message,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Hide Offcanvas
                        const offcanvasEl = document.getElementById("followUpCanvas");
                        const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                        offcanvas.hide();

                        // Optional: Reset form
                        $("#followUpForm")[0].reset();

                        // Reload DataTable
                        $('#leadsTable').DataTable().ajax.reload();
                    }
                },

                error: function(err) {

                    Swal.fire({
                        title: "Validation Error!",
                        text: "Please check required fields.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            });
        });

        var followupRoute = "{{ route('get.followups', ['phone' => '__phone__']) }}";

        $(document).on('click', '.viewFollowUp', function() {
            let phone = $(this).data('phone');

            let url = followupRoute.replace('__phone__', phone);

            $.ajax({
                url: url,
                type: "GET",
                success: function(res) {

                    let html = "";

                    if (res.data.length === 0) {
                        html = `<div class="alert alert-warning">No follow-ups found.</div>`;
                    } else {
                        res.data.forEach(function(item) {
                            html += `
                                   <div class="card border-0 mt-4 mb-4 rounded-3 shadow bg-body-tertiar">
                                       <div class="card-body p-6">

                                           <div class="d-flex justify-content-between align-items-center mb-2">
                                               <span class="badge bg-primary px-3 py-2">
                                                   <i class="ph ph-check-circle me-1"></i> ${item.status}
                                               </span>
                                               <small class="text-muted">
                                                   <i class="ph ph-calendar me-1"></i> ${item.formatted_date}
                                                   <i class="ph ph-clock me-1 ms-2"></i> ${item.formatted_time}
                                               </small>
                                           </div>

                                           <p class="mb-2" style="font-size: 14px;">
                                               <i class="ph ph-chat-circle-dots me-2 text-info"></i>
                                               <strong>Comment:</strong> ${item.comment}
                                           </p>

                                           <div class="d-flex align-items-center mt-2">
                                               <i class="ph ph-user-circle me-2 text-secondary" style="font-size:20px;"></i>
                                               <span class="fw-semibold" style="font-size:14px;">${item.users_name}</span>
                                           </div>

                                       </div>
                                   </div>
                                `;

                        });
                    }

                    $("#followUpContent").html(html);

                    // open offcanvas
                    var offcanvas = new bootstrap.Offcanvas(document.getElementById(
                        'ViewfollowUpCanvas'));
                    offcanvas.show();
                }
            });
        });
    </script>
@endpush
