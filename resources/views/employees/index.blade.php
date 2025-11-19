@extends('layouts.app_layout')
@section('style')
    <style>
        #eml_password-error {
            position: absolute;
            top: 95%;
        }

        #empl_password_confirmation-error {
            position: absolute;
            top: 95%;
        }
    </style>
@endsection
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Employees</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Employees</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <!--table wrapper -->
            <div class="card mb-0">
                <div class="mb-3 filter-col">
                    <div class="col-md-3 form-group">
                        <label for="filter_status">Status Filter</label>
                        <select class="form-control form-select" name="filter_status" id="filter_status">
                            <option value="">Select</option>
                            <option value="active">
                                Active</option>
                            <option value="inactive">
                                Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table id="employee_table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!--end table wrapper-->

            <!-- employee entry form  -->
            <div class="offcanvas offcanvas-end custom-offcanvas-50" tabindex="-1" id="offcanvasEmployeeForm"
                aria-labelledby="offcanvasEmployeeFormLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasEmployeeFormLabel">Add Employee</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <form id="employeeForm" method="POST" action="javascript:void(0)">
                        <input type="hidden" name="operation_type" id="operation_type" value="EDIT">
                        <input type="hidden" name="hidden_id" id="hidden_id">
                        <input type="hidden" name="form_action" id="form_action">
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-lg-6">
                                <h6 class="mb-3 fw-semibold d-flex align-items-center border-bottom py-2">
                                    <i class="bx bx-user-circle"></i>&nbsp;Personal Information
                                </h6>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter employee's full name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter email address" required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number
                                    </label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        placeholder="Enter phone number">
                                </div>

                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth
                                    </label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="col-lg-6">
                                <h6 class="mb-3 fw-semibold d-flex align-items-center border-bottom py-2">
                                    <i class="bx bx-briefcase"></i>&nbsp;Professional Information
                                </h6>
                                <div class="mb-3">
                                    <label for="position" class="form-label">Position/Job Title
                                    </label>
                                    <input type="text" class="form-control" id="position" name="position"
                                        placeholder="Enter job position">
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-control" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="active">
                                            Active</option>
                                        <option value="inactive">
                                            Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="col-12 mt-2">
                                <h6 class="mb-3 fw-semibold d-flex align-items-center border-bottom py-2">
                                    <i class="bx bx-map-pin"></i>&nbsp;Address Information
                                </h6>

                                <div class="">
                                    <label for="address" class="form-label">Address
                                    </label>
                                    <textarea class="form-control" id="empl_address" name="address" rows="2" placeholder="Enter full address"></textarea>
                                </div>
                            </div>

                            <!-- Security Information -->
                            <div class="col-12 mt-2">
                                <h6 class="mb-3 fw-semibold d-flex align-items-center border-bottom py-2 ">
                                    <i class="bx bx-shield"></i>&nbsp;Security Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password <span
                                                    class="text-danger password-star">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="eml_password"
                                                    name="password" placeholder="Enter password" autocomplete="off">
                                                <span class="input-group-text cursor-pointer"
                                                    onclick="togglePassword('eml_password')">
                                                    <i class="bx bx-hide eml_password"></i>&nbsp;
                                                </span>
                                            </div>
                                            <div class="passwordInstruction">
                                                <small class="form-text text-muted ">Password must be at
                                                    least 8
                                                    characters long.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="empl_password_confirmation" class="form-label">Confirm Password
                                                <span class="text-danger confirm-star">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password" class="form-control"
                                                    id="empl_password_confirmation" name="password_confirmation" autocomplete="off"
                                                    placeholder="Confirm password">
                                                <span class="input-group-text cursor-pointer"
                                                    onclick="togglePassword('empl_password_confirmation')">
                                                    <i class="bx bx-hide empl_password_confirmation"></i>&nbsp;
                                                </span>
                                            </div>
                                        </div>
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
            <!-- end employee entry form  -->
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

            let myOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasEmployeeForm'));

            const employeeDataTable = $('#employee_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollX: true,
                scrollCollapse: true,
                pagination: true,
                ajax: {
                    url: `${base_url}/admin/ajax/get/all-employees`,
                    type: 'POST',
                    global: false,
                    data: function(d) {
                        d.filter_status = $('#filter_status').val();
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
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'position',
                        name: 'position'
                    },
                    {
                        data: 'full_status',
                        name: 'status'
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
                    "targets": [0, 1, 2, 3, 4, 5],
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
                    `<button type="button" class="btn btn-primary" type="button" id="addEmployeeBtn"><i
                            class="lni lni-circle-plus mx-1"></i>Add New
                        Employee</button>`
                );
            })

            // On click add button, open the modal
            $(document).on('click', '#addEmployeeBtn', function() {
                document.getElementById("employeeForm").reset();
                $("#employeeForm").validate().resetForm();
                $('#operation_type').val('ADD');
                $("#form_action").val(`{{ route('employees.store') }}`);
                $("#hidden_id").val('');
                $('#offcanvasEmployeeFormLabel').html('<i class="bx bx-plus"></i> Add New Employee');
                $('#formSubmitBtn').html('<i class="bx bx-check-circle"></i> Create Employee');
                $("#eml_password").prop('disabled', false);
                $("#empl_password_confirmation").prop('disabled', false);
                $("#status").prop('disabled', false);
                myOffcanvas.toggle();
            });

            // Filter functionality
            $('#filter_status').on('change', () => employeeDataTable.ajax.reload());

            $("#cacnelBtn").on('click', function() {
                myOffcanvas.toggle();
            });

            // jQuery Validation
            $("#employeeForm").validate({
                errorClass: "text-danger validation-error",
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    date_of_birth: {
                        date: true
                    },
                    position: {
                        minlength: 2
                    },
                    status: {
                        required: true
                    },
                    address: {
                        minlength: 5
                    },
                    password: {
                        required: function() {
                            return $('#operation_type').val() === 'ADD';
                        },
                        minlength: 8
                    },
                    password_confirmation: {
                        required: function() {
                            return $('#operation_type').val() === 'ADD';
                        },
                        minlength: 8,
                        equalTo: "#eml_password"
                    }
                },

                messages: {
                    name: {
                        required: "Please enter full name",
                        minlength: "Name must be at least 3 characters"
                    },
                    email: {
                        required: "Please enter your email",
                        email: "Please enter a valid email"
                    },
                    phone: {
                        digits: "Phone number must contain only digits",
                        minlength: "Phone number must be 10 digits",
                        maxlength: "Phone number must be 10 digits"
                    },
                    date_of_birth: {
                        date: "Please enter valid date"
                    },
                    position: {
                        minlength: "Position must be at least 2 characters"
                    },
                    status: {
                        required: "Please select a status"
                    },
                    address: {
                        minlength: "Address must be at least 5 characters"
                    },
                    password: {
                        required: "Password is required",
                        minlength: "Password must be at least 8 characters"
                    },
                    password_confirmation: {
                        required: "Confirm Password is required",
                        minlength: "Confirm Password must be at least 8 characters",
                        equalTo: "Password and Confirm Password do not match"
                    }
                },

                showErrors: function(errorMap, errorList) {
                    this.defaultShowErrors();
                    if ($("#eml_password-error").is(":visible")) {
                        $(".passwordInstruction").addClass("mt-3");
                    } else {
                        $(".passwordInstruction").removeClass("mt-3");
                    }
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
                                employeeDataTable.ajax.reload();
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
                $("#employeeForm")[0].reset();
                $("#employeeForm").validate().resetForm();
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
                            // Set the form data
                            formReset();
                            $('#operation_type').val('EDIT');
                            $('#hidden_id').val(data.id);
                            $("#form_action").val(updateRoute);
                            $('#name').val(data.name);
                            $('#email').val(data.email);
                            $('#phone').val(data.phone);
                            $('#empl_address').val(data.address);
                            $('#position').val(data.position);
                            $('#salary').val(data.salary);
                            var dob = data.date_of_birth;
                            if (data.date_of_birth) {
                                dob = data.date_of_birth.split('T')[
                                    0];
                            }
                            // Extract "YYYY-MM-DD" part
                            $('#date_of_birth').val(dob);

                            $('#status option[value="' + data.status + '"]').prop('selected',
                                true);

                            $("#eml_password").prop('disabled', true);
                            $("#empl_password_confirmation").prop('disabled', true);
                            $("#status").prop('disabled', true);


                            $('#offcanvasEmployeeFormLabel').html(
                                '<i class="bx bx-edit"></i> Edit Employee');
                            $('#formSubmitBtn').html(
                                '<i class="bx bx-edit"></i> Update Employee');
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
                                url: base_url + `/admin/employees/${id}`,
                                type: 'DELETE',
                                success: function(response) {
                                    if (response.status == true) {
                                        toastr.success(response.message);
                                        employeeDataTable.ajax.reload();
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

            $(document).on('click', '.deactivateBtn', function() {
                var id = $(this).attr('id');
                if (id) {
                    $.ajax({
                        url: base_url + `/admin/employees/${id}/toggle-status`,
                        type: 'POST',
                        success: function(response) {
                            if (response.status == true) {
                                toastr.success(response.message);
                                employeeDataTable.ajax.reload();
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
                } else {
                    toastr.error('Something went wrong. Please try again.');
                }

            });
        });

        function togglePassword(cls) {
            let input = $("#" + cls);
            let icon = $("." + cls);

            if (icon.hasClass("bx-hide")) {
                input.attr("type", "text");
                icon.removeClass("bx-hide").addClass("bx-show");
            } else {
                icon.removeClass("bx-show").addClass("bx-hide");
                input.attr("type", "password");
            }
        }
    </script>
@endsection
