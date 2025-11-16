@extends('layouts.app_layout')
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Leads</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Leads</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <!--table wrapper -->
            <div class="card mb-0">
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table id="location_table" class="table table-striped table-bordered mt-2" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>User Name</th>
                                    <th>Phone</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Crurrent FollowUp Status</th>
                                    <th>Last Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!--end table wrapper-->

            <!-- entry form  -->
            <div class="offcanvas offcanvas-end custom-offcanvas-50" tabindex="-1" id="offcanvasForm"
                aria-labelledby="offcanvasFormLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasFormLabel"></h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <form id="tableForm" method="POST" action="javascript:void(0)">
                        <input type="hidden" name="operation_type" id="operation_type" value="EDIT">
                        <input type="hidden" name="hidden_id" id="hidden_id">
                        <input type="hidden" name="form_action" id="form_action">
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-lg-12">
                                <h6 class="mb-3 fw-semibold d-flex align-items-center border-bottom py-2">
                                    <i class="bx bx-user-circle"></i>&nbsp;Add Follow-Up
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Type <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-select form-control" required>
                                            <option value="">-- Select Status --</option>
                                            <option value="busy">Busy</option>
                                            <option value="interested">Interested</option>
                                            <option value="materialised">Materialised</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="" id="busyFields" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Next Follow-up Date</label>
                                                    <input type="date" name="next_followup_date" class="form-control"
                                                        min="@php echo date('Y-m-d'); @endphp">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Next Follow-up Time</label>
                                                    <input type="time" name="next_followup_time" class="form-control"
                                                        min="@php echo date('H:i'); @endphp">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Comment</label>
                                        <textarea name="comment" class="form-control" required></textarea>
                                    </div>

                                </div>


                            </div>

                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4 mb-5">
                            <div class="col-12">
                                <div class="d-flex  justify-content-between align-items-center mx-12">
                                    <button type="submit" class="btn btn-primary d-flex align-items-center"
                                        id="formSubmitBtn">
                                    </button>
                                    <button class="btn btn-outline-danger d-flex align-items-center" type="button"
                                        id="cacnelBtn">
                                        <i class="bx bx-x"></i>&nbsp;Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end entry form  -->

            {{-- show offcanvas --}}
            <div class="offcanvas offcanvas-end custom-offcanvas-50" tabindex="-1" id="offcanvasShow"
                aria-labelledby="offcanvasShowLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasShowLabel"></h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" id="showFollowUpBody">

                </div>
            </div>
            {{-- show offcanvas --}}


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

            let myOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasForm'));

            const dataTableList = $('#location_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollX: true,
                scrollCollapse: true,
                pagination: true,
                ajax: {
                    url: `${base_url}/employee/ajax/get/all-leads`,
                    type: 'POST',
                    global: false,
                    data: function(d) {
                        // d.type = 'visitor';
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
                        data: 'follow_up_status',
                        name: 'follow_up_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'follow_status_badge',
                        name: 'follow_status_badge',
                        orderable: false,
                        searchable: false
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
                    "targets": '-All',
                    "orderable": false,
                    "sorting": false
                }],
                dom: "<'row'<'col-12 col-md-4'B><'col-12 col-md-4'l><'col-12 col-md-4'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
                buttons: [],

            });


            // Show/hide fields based on status
            $("#status").on("change", function() {
                if ($(this).val() === "busy") {
                    $("#busyFields").slideDown();
                    // Make fields required dynamically
                    $("[name='next_followup_date']").rules("add", {
                        required: true
                    });
                    $("[name='next_followup_time']").rules("add", {
                        required: true
                    });
                } else {
                    $("#busyFields").slideUp();

                    // Remove validation rules dynamically
                    $("[name='next_followup_date']").rules("remove", "required");
                    $("[name='next_followup_time']").rules("remove", "required");

                    // Also clear validation error messages
                    $("[name='next_followup_date']").val("").removeClass("error");
                    $("[name='next_followup_time']").val("").removeClass("error");
                }
            });




            // On click add button, open the modal
            $(document).on('click', '.addBtn', function() {
                document.getElementById("tableForm").reset();
                $('#operation_type').val('ADD');
                $("#tableForm").validate().resetForm();
                $("#form_action").val(`{{ route('leads.store') }}`);
                let phone = $(this).data('phone');
                $("#hidden_id").val(phone);
                $('#offcanvasFormLabel').html('<i class="bx bx-plus"></i> Save Follow-Up');
                $('#formSubmitBtn').html('<i class="bx bx-check-circle me-1"></i> Create Lead');
                myOffcanvas.toggle();
            });

            // Filter functionality
            // $('#filter_status').on('change', () => dataTableList.ajax.reload());





            // jQuery Validation
            $("#tableForm").validate({
                errorClass: "text-danger validation-error",
                rules: {
                    status: {
                        required: true
                    },
                    comment: {
                        required: true,
                        minlength: 3
                    },
                    next_followup_date: {
                        required: function() {
                            return $("#status").val() === "busy";
                        }
                    },
                    next_followup_time: {
                        required: function() {
                            return $("#status").val() === "busy";
                        }
                    }
                },
                messages: {
                    next_followup_date: {
                        required: "Please select next follow-up date"
                    },
                    next_followup_time: {
                        required: "Please select next follow-up time"
                    }
                },

                messages: {
                    loc_name: {
                        required: "Please enter full name",
                        minlength: "Name must be at least 3 characters"
                    },
                    phone: {
                        required: "Please enter Address",
                        minlength: "Phone number must be 3 digits",

                    },
                    type: {
                        required: "Please select type"
                    },
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var formData = new FormData(form);
                    const formAction = $('#form_action').val();
                    var operationType = $('#operation_type').val();
                    $.ajax({
                        url: formAction,
                        type: 'POST',
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json",

                        success: function(response) {
                            if (response.status === true) {
                                formReset();
                                myOffcanvas.toggle();
                                toastr.success(response.message);
                                // location.reload();
                                dataTableList.ajax.reload();
                            } else if (response.status === 'validation_error') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    html: response.message
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },

                        error: function() {
                            toastr.error('Server error. Please try again.');
                        }
                    });
                }
            });

            function formReset() {
                $("#tableForm")[0].reset();
                $("#tableForm").validate().resetForm();
                $(".validation-error").removeClass("validation-error");

                // Restore states
                $('#loc_name, #loc_address, #numTables, #type').prop('readonly', false);
                $('#type').prop('disabled', false);
                $('#formSubmitBtn').show();
                $('#image').closest('.mb-3').show();

                $("#busyFields").slideUp();

                // Remove validation rules dynamically
                $("[name='next_followup_date']").rules("remove", "required");
                $("[name='next_followup_time']").rules("remove", "required");

                // Also clear validation error messages
                $("[name='next_followup_date']").val("").removeClass("error");
                $("[name='next_followup_time']").val("").removeClass("error");
            }


            $("#cacnelBtn").on('click', function() {
                myOffcanvas.toggle();
            });

            $('#offcanvasForm').on('hidden.bs.offcanvas', function() {
                formReset();
            });





            // show detals in view modal
            $(document).on('click', '.ViewBtn', function() {

                let route = $(this).attr('editRoute');

                $.ajax({
                    url: route,
                    type: "GET",
                    success: function(res) {

                        if (res.status !== true) {
                            toastr.error("Unable to fetch lead details");
                            return;
                        }

                        let data = res.data;

                        // --------------------
                        // Build HTML for offcanvas
                        // --------------------
                        let html = `
                <div class="mb-3">
                    <h5 class="fw-bold mb-1">${data.contact.name}</h5>
                    <p class="text-muted mb-0">
                        <i class="ph ph-phone me-1"></i> ${data.contact.phone}
                    </p>
                </div>

                <hr>

                <h6 class="fw-bold text-primary mb-3">
                    <i class="ph ph-info me-1"></i> Latest Follow-Up
                </h6>
                ${buildLatestFollowupCard(data.latest_followup)}

                <hr>

                <h6 class="fw-bold text-primary mb-3">
                    <i class="ph ph-clock-counter-clockwise me-1"></i> Follow-Up History
                </h6>
                ${buildHistoryCards(data.history)}
            `;

                        $("#showFollowUpBody").html(html);

                        // Open offcanvas
                        var offcanvas = new bootstrap.Offcanvas(document.getElementById(
                            'offcanvasShow'));
                        $("#offcanvasShowLabel").html(
                            `<i class="bx bx-show"></i> Lead Follow-up Details`);
                        offcanvas.show();
                    }
                });
            });


            function buildLatestFollowupCard(item) {

                return `
        <div class="card border-0 rounded-3 shadow mb-4">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-success px-3 py-2">${item.status}</span>
                    <small class="text-muted">
                        <i class="ph ph-calendar me-1"></i> ${formatDate(item.created_at)}
                        <i class="ph ph-clock me-1 ms-2"></i> ${formatTime(item.created_at)}
                    </small>
                </div>

                <p><strong>Comment:</strong> ${item.comment}</p>

                ${item.next_followup_date ? `
                                            <p class="mb-1"><strong>Next Follow-Up:</strong> ${item.next_followup_date}</p>
                                            <p class="mb-0"><strong>Time:</strong> ${item.next_followup_time}</p>
                                            ` : ""}
            </div>
        </div>
         `;
            }



            function buildHistoryCards(history) {
                if (!history || history.length === 0) {
                    return `<div class="alert alert-warning">No follow-ups found.</div>`;
                }

                let html = "";

                history.forEach(item => {
                    html += `
            <div class="card border-0 mt-3 mb-3 shadow rounded-3">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-primary px-3 py-2">${item.status}</span>
                        <small class="text-muted">
                            <i class="ph ph-calendar me-1"></i> ${item.formatted_date}
                            <i class="ph ph-clock me-1 ms-2"></i> ${item.formatted_time}
                        </small>
                    </div>

                    <p class="mb-2">
                        <strong>Comment:</strong> ${item.comment}
                    </p>

                    <div class="d-flex align-items-center">
                        <i class="ph ph-user-circle me-2 text-secondary" style="font-size:20px;"></i>
                        <span class="fw-semibold">${item.users_name}</span>
                    </div>

                </div>
            </div>
        `;
                });

                return html;
            }



            function formatDate(dateString) {
                let d = new Date(dateString);
                return d.toLocaleDateString('en-GB'); // dd/mm/yyyy
            }

            function formatTime(dateString) {
                let d = new Date(dateString);
                return d.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }



        });
    </script>
@endsection
