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
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role . '.dashboard') }}"><i
                                        class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Leads</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <!--table wrapper -->
            <div class="card mb-0">
                <div class="mb-3 filter-col">
                    <div class="col-md-3 form-group">
                        <label for="filter_lead_type">Status Lead type</label>
                        <select class="form-control form-select" name="filter_lead_type" id="filter_lead_type">
                            <option value="">Select</option>
                            <option value="visitor">
                                Visitor</option>
                            <option value="exhibitor">
                                Exhibitor</option>
                        </select>
                    </div>
                </div>
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

                                        <!-- Materialised Fields -->
                                        <div id="materialisedFields" class="mt-3" style="display: none;">
                                            <div class="row">

                                                <div class="col-md-6 mb-3">

                                                    <label class="form-label">Booking Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" name="booking_date" class="form-control"
                                                        min="@php echo date('Y-m-d'); @endphp">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Booking Locations <span
                                                            class="text-danger">*</span></label>
                                                    <select name="booking_location" id="booking_location"
                                                        class="form-select form-control select2" required>
                                                        <option value="">-- Select Location --</option>
                                                        @foreach ($location as $loc)
                                                            <option value="{{ $loc->id }}">
                                                                {{ $loc->loc_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Table No <span
                                                            class="text-danger">*</span></label>
                                                    <select name="table_no" id="table_no"
                                                        class="form-select form-control select2"></select>
                                                    {{-- <option value="">-- Select Table No --</option> --}}

                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Price <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="price" class="form-control"
                                                        placeholder="Enter Price" readonly>
                                                </div>
                                                {{-- <div class="col-md-12">
                                                    <button type="button" id="checkAvailabilityBtn"
                                                        class="btn btn-warning mt-2">
                                                        Check Availability
                                                    </button>
                                                </div> --}}


                                                <div id="availabilityResult" class="mt-2 fw-bold">

                                                </div>

                                                <div class="col-md-6 mt-2">
                                                    <label class="form-label">Amount Status <span
                                                            class="text-danger">*</span></label>
                                                    <select name="amount_status" class="form-select form-control"
                                                        id="amount_status">
                                                        <option value="">-- Select Amount Status --</option>
                                                        <option value="partial" selected>Partial</option>
                                                        <option value="paid"> Paid</option>
                                                        <option value="unpaid"> Unpaid</option>
                                                    </select>
                                                </div>
                                                <div id="amountPaidWrapper" class="col-md-6 mt-2">
                                                    <label class="form-label">Amount Paid <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="amount_paid" class="form-control"
                                                        placeholder="Enter Amount Paid">
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
                        d.filter_lead_type = $('#filter_lead_type').val();
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
                buttons: []

            });

            $(window).on('load', function() {
                $('.dataTables_wrapper .dt-buttons').append(
                    `<button type="button" class="btn btn-primary" type="button" id="addNewBtn"><i
                            class="lni lni-circle-plus mx-1"></i>Add New</button>`
                );
            })


            // Show/hide fields based on status
            $("#status").on("change", function() {
                let status = $(this).val();

                if (status === "busy") {
                    $("#busyFields").slideDown();
                    $("#materialisedFields").slideUp();
                } else if (status === "materialised") {
                    $("#materialisedFields").slideDown();
                    $("#busyFields").slideUp();
                } else {
                    $("#busyFields").slideUp();
                    $("#materialisedFields").slideUp();
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
            $('#filter_lead_type').on('change', () => dataTableList.ajax.reload());





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

                    // Busy
                    next_followup_date: {
                        required: function() {
                            return $("#status").val() === "busy";
                        }
                    },
                    next_followup_time: {
                        required: function() {
                            return $("#status").val() === "busy";
                        }
                    },

                    // Materialised rules
                    booking_date: {
                        required: function() {
                            return $("#status").val() === "materialised";
                        }
                    },
                    booking_location: {
                        required: function() {
                            return $("#status").val() === "materialised";
                        }
                    },
                    table_no: {
                        required: function() {
                            return $("#status").val() === "materialised";
                        }
                    },
                    price: {
                        required: function() {
                            return $("#status").val() === "materialised";
                        }
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


                $("#materialisedFields").hide();
                $("[name='booking_date']").val("").removeClass("error");
                $("[name='booking_location']").val("").removeClass("error");
                $("[name='table_no']").val("").removeClass("error");
                $("[name='price']").val("").removeClass("error");

            }


            $("#cacnelBtn").on('click', function() {
                myOffcanvas.toggle();
            });

            $('#offcanvasForm').on('hidden.bs.offcanvas', function() {
                formReset();
            });





            // show detals in view offcanvas
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
                              ${buildHistoryCards(data.history)}`;

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


            // build latest followup card
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
                                                                                                                        <p class="mb-0"><strong>Time:</strong> ${item.next_followup_time}</p> ` : ""}
                </div>
              </div>
             `;
            }


            // build history cards
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





            // Booking location change - load table numbers
            $('#booking_location').on('change', function() {

                let locationId = $(this).val();

                $('#table_no').empty();
                $('#table_no').append('<option value="">-- Select Table No --</option>');

                if (locationId) {

                    let url = "{{ route('booking.getTables', ':id') }}";
                    url = url.replace(':id', locationId);

                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function(response) {
                            $.each(response, function(index, table) {
                                $('#table_no').append(
                                    '<option value="' + table.id + '">' + table
                                    .table_no + '</option>'
                                );
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: 'Failed to load table numbers.'
                            });

                        }
                    });
                }
            });



            // Table no change - load price
            $('#table_no').on('change', function() {

                let tableId = $(this).val();

                // Clear existing price
                $('input[name="price"]').val('');

                if (tableId) {

                    let url = "{{ route('booking.getPrice', ':id') }}";
                    url = url.replace(':id', tableId);

                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function(response) {
                            $('input[name="price"]').val(response.price);
                            checkAvailabilityBtn();
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: 'Failed to load price.'
                            });
                        }
                    });
                }
            });


            function checkAvailabilityBtn() {

                let booking_date = $("[name='booking_date']").val();
                let booking_location = $("[name='booking_location']").val();
                let table_no = $("[name='table_no']").val();
                let price = $("[name='price']").val();

                if (!booking_date || !booking_location || !table_no || !price) {
                    $("#availabilityResult").html(
                        "<span class='text-danger'>Please fill all fields first.</span>");
                    return;
                }

                $.ajax({
                    url: "{{ route('booking.checkAvailability') }}", // ADD THIS ROUTE
                    type: "POST",
                    data: {
                        booking_date: booking_date,
                        booking_location: booking_location,
                        table_no: table_no,
                        price: price,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {

                        if (response.available === true) {
                            $("#availabilityResult").html(
                                "<span class='text-success'>Available ✓</span>");
                        } else {
                            $("#availabilityResult").html(
                                "<span class='text-danger'>Not Available ✗</span>");
                        }
                    },
                    error: function(xhr) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: xhr.responseText
                        });
                    }
                });

            };



            // $('#booking_location').select2({
            //     // booking.searchLocation
            //     ajax: {
            //         url: `${base_url}/api/search-location`,
            //         dataType: 'json',
            //         delay: 250,
            //         data: function(params) {
            //             return {
            //                 term: params.term //
            //             };
            //         },
            //         processResults: function(data) {
            //             return {
            //                 results: data //
            //             };
            //         }
            //     },
            //     minimumInputLength: 2,
            //     placeholder: 'Location चुनें'
            // });



            $('#amount_status').on('change', function() {
                let status = $(this).val();
                let price = $('input[name="price"]').val();
                let amountPaidInput = $('input[name="amount_paid"]');
                let wrapper = $('#amountPaidWrapper'); // wrapper div

                if (status === "paid") {
                    wrapper.show();
                    amountPaidInput.val(price).prop('readonly', true);
                } else if (status === "partial") {
                    wrapper.show();
                    amountPaidInput.val("").prop('readonly', false);
                } else if (status === "unpaid") {
                    wrapper.hide();
                    amountPaidInput.val("").prop('readonly', false);
                }
            });


        });
    </script>
@endsection
