@extends('layouts.app_layout')
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Visitors</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Visitors</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <!--table wrapper -->
            <div class="card mb-0">
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table id="employee_table" class="table table-striped table-bordered mt-2" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Phone Number</th>
                                    <th>Action</th>
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
                        <input type="hidden" name="type" id="type" value="visitor">
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-lg-6">
                                <h6 class="mb-3 fw-semibold d-flex align-items-center border-bottom py-2">
                                    <i class="bx bx-user-circle"></i>&nbsp;Basic Information
                                </h6>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter full name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="location" name="location"
                                        placeholder="Enter location" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h6 class="mb-3 fw-semibold d-flex align-items-center border-bottom py-2">
                                    <i class="bx bx-phone"></i>&nbsp;Contact Information
                                </h6>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="Enter phone number" required>
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

            const dataTableList = $('#employee_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollX: true,
                scrollCollapse: true,
                pagination: true,
                ajax: {
                    url: `${base_url}/admin/ajax/get/all-contacts`,
                    type: 'POST',
                    global: false,
                    data: function(d) {
                        d.type = 'visitor';
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
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
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
                    "targets":'-All',
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
                            class="lni lni-circle-plus mx-1"></i>Add New
                        Visitor</button>`
                );
            })

            // On click add button, open the modal
            $(document).on('click', '#addNewBtn', function() {
                document.getElementById("tableForm").reset();
                $("#tableForm").validate().resetForm();
                $('#operation_type').val('ADD');
                $("#form_action").val(`{{ route('contacts.store') }}`);
                $("#hidden_id").val('');
                $('#offcanvasFormLabel').html('<i class="bx bx-plus"></i> Add New Visitor');
                $('#formSubmitBtn').html('<i class="bx bx-check-circle"></i> Create Visitor');
                $("#eml_password").prop('disabled', false);
                $("#empl_password_confirmation").prop('disabled', false);
                $("#status").prop('disabled', false);
                myOffcanvas.toggle();
            });

            // Filter functionality
            $('#filter_status').on('change', () => dataTableList.ajax.reload());

            $("#cacnelBtn").on('click', function() {
                myOffcanvas.toggle();
            });

            // jQuery Validation
            $("#tableForm").validate({
                errorClass: "text-danger validation-error",
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    phone: {
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    location: {
                        minlength: 5
                    },
                },

                messages: {
                    name: {
                        required: "Please enter full name",
                        minlength: "Name must be at least 3 characters"
                    },
                    phone: {
                        digits: "Phone number must contain only digits",
                        minlength: "Phone number must be 10 digits",
                        maxlength: "Phone number must be 10 digits"
                    },
                    location: {
                        minlength: "Location must be at least 5 characters"
                    },
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var formData = new FormData(form);
                    const formAction = $('#form_action').val();
                    var operationType = $('#operation_type').val();
                    if (operationType == 'EDIT') {
                        formData.append('_method', 'PUT');
                    }

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
                                dataTableList.ajax.reload();
                                formReset();
                                myOffcanvas.toggle();
                                toastr.success(response.message);
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
            };

            // handle table action button click
            $(document).on('click', '.editBtn', function() {
                const route = $(this).attr('editRoute');
                const updateRoute = $(this).attr("updateRoute");
                $.ajax({
                    url: route,
                    type: 'GET',
                    success: function(response) {
                        if (response.status == true) {
                            var data = response.data;
                            console.log('data', data);

                            // Set the form data
                            formReset();
                            $('#operation_type').val('EDIT');
                            $('#hidden_id').val(data.id);
                            $("#form_action").val(updateRoute);
                            $('#name').val(data.name);
                            $('#phone').val(data.phone);
                            $('#location').val(data.location);

                            $('#offcanvasFormLabel').html(
                                '<i class="bx bx-edit"></i> Edit Visitor');
                            $('#formSubmitBtn').html(
                                '<i class="bx bx-edit"></i> Update Visitor');
                            myOffcanvas.toggle();
                        } else if (response.status == false) {
                            toastr.error(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(errors) {
                        console.log(errors);
                    }
                });
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
                                url: base_url + `/admin/contacts/${id}`,
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
        });
    </script>
@endsection
