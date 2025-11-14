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
        <div class="offcanvas offcanvas-end shadow" tabindex="-1" id="followUpCanvas">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Add Follow-Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body">

                <form id="followUpForm">
                    @csrf
                    <input type="hidden" name="user_id" id="userId" readonly>

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
                        <input type="date" name="next_followup_date" class="form-control">

                        <label class="form-label mt-2">Next Follow-up Time</label>
                        <input type="time" name="next_followup_time" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="comment" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Save Follow-Up</button>

                </form>

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
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'follow_status_badge',
                    name: 'follow_status_badge'
                },
                {
                    data: 'follow_up_status',
                    name: 'follow_up_status'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                },
            ]
        });


        // Open follow-up offcanvas
        $(document).on("click", ".openFollowUp", function() {

            let userId = $(this).data("id");

            // Set user ID
            $("#userId").val(userId);

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
    </script>
@endpush
