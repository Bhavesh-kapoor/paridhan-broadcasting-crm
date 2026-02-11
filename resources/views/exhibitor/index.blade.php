@extends('layouts.app_layout')
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Exhibitors Contact</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role . '.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Exhibitors Contact</li>
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
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Location</th>
                                    <th>GST Number</th>
                                    <th>Product Type</th>
                                    <th>Brand Name</th>
                                    <th>Business Type</th>
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
                    <h5 id="offcanvasFormLabel">Add Employee</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <form id="tableForm" method="POST" action="javascript:void(0)">
                        <input type="hidden" name="operation_type" id="operation_type" value="EDIT">
                        <input type="hidden" name="hidden_id" id="hidden_id">
                        <input type="hidden" name="form_action" id="form_action">
                        <input type="hidden" name="type" id="type" value="exhibitor">
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

                                <div class="mb-3">
                                    <label for="alternate_phone" class="form-label">Alternate Phone
                                    </label>
                                    <input type="text" class="form-control" id="alternate_phone" name="alternate_phone"
                                        placeholder="Enter alternate phone">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <h6 class="mb-3 fw-semibold d-flex align-items-center border-bottom py-2">
                                    <i class="bx bx-store-alt"></i>&nbsp;Business Information
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter email address" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="gst_number" class="form-label">GST Number
                                        </label>
                                        <input type="text" class="form-control" id="gst_number" name="gst_number"
                                            placeholder="Enter GST number">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="product_type" class="form-label">Product Type
                                        </label>
                                        <input type="text" class="form-control" id="product_type" name="product_type"
                                            placeholder="Enter product Type">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="brand_name" class="form-label">Brand Name
                                        </label>
                                        <input type="text" class="form-control" id="brand_name" name="brand_name"
                                            placeholder="Enter brand Name">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="business_type" class="form-label">Business Type
                                        </label>
                                        <select class="form-select form-control" id="business_type" name="business_type">
                                            <option value="">--Select Business Type--</option>
                                            @foreach (Config::get('contants.business_types') as $key => $psl)
                                                <option value="{{ $key }}">{{ $psl }}</option>
                                            @endforeach
                                        </select>
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


            <!-- bulk import form   -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBulImportForm"
                aria-labelledby="offcanvasBulImportFormLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasBulImportFormLabel"><i class="bx bx-file"></i> Bulk Import</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>

                <div class="offcanvas-body">
                    <form id="bulkUploadForm" method="POST" action="javascript:void(0)" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="d-flex  justify-content-between align-items-baseline">
                                        <label for="file" class="form-label">File <span class="text-danger">*</span>

                                        </label>
                                        <a href="{{ asset('assets/excel/uploadformat.csv') }}" download
                                            class="form-text text-danger  fs-6">
                                            <i class="bx bx-file"></i>&nbsp;Download Sample Format
                                        </a>
                                    </div>


                                    <input type="file" class="form-control mt-1" id="file" name="file"
                                        required>
                                </div>

                                <div class="col-12 mt-4 mb-5">
                                    <div class="d-flex  justify-content-between align-items-center mx-12">
                                        <button type="submit" class="btn btn-primary d-flex align-items-center"
                                            id="blkFormSubmitBtn">
                                            <i class="bx bx-plus"></i> Upload
                                        </button>
                                        <button class="btn btn-outline-danger d-flex align-items-center" type="button"
                                            id="blkClancelBtn">
                                            <i class="bx bx-x"></i>&nbsp;Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <!-- bulk import form   -->
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
            let bulkUploadFormCanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasBulImportForm'));

            const dataTableList = $('#employee_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollX: true,
                scrollCollapse: true,
                pagination: true,
                searchDelay: 300,
                deferRender: true,
                ajax: {
                    url: `${base_url}/admin/ajax/get/all-contacts`,
                    type: 'POST',
                    global: false,
                    data: function(d) {
                        d.type = 'exhibitor';
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
                        data: 'location',
                        name: 'location'
                    },

                    {
                        data: 'gst_number',
                        name: 'gst_number'
                    },
                    {
                        data: 'product_type',
                        name: 'product_type'
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name'
                    },
                    {
                        data: 'business_type',
                        name: 'business_type'
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
                buttons: [],

            });

            $(window).on('load', function() {
                $('.dataTables_wrapper .dt-buttons').append(
                    `<button type="button" class="btn btn-primary" type="button" id="addNewBtn"><i
                    class="lni lni-circle-plus mx-1"></i>Add New
                Exhibitor Contact</button><button type="button" class="btn btn-warning" type="button" id="bulkImportBtn"><i
                    class="bx bx-file mx-1"></i>
                Bulk Import</button>`
                );
            })

            // On click add button, open the modal
            $(document).on('click', '#addNewBtn', function() {
                document.getElementById("tableForm").reset();
                $("#tableForm").validate().resetForm();
                $('#operation_type').val('ADD');
                $("#form_action").val(`{{ route('contacts.store') }}`);
                $("#hidden_id").val('');
                $('#offcanvasFormLabel').html('<i class="bx bx-plus"></i> Add New Exhibitor');
                $('#formSubmitBtn').html('<i class="bx bx-check-circle"></i> Create Exhibitor');
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
                    email: {
                        required: true,
                        email: true
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
                    email: {
                        required: "Please enter your email",
                        email: "Please enter a valid email"
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
                            formReset();
                            $('#operation_type').val('EDIT');
                            $('#hidden_id').val(data.id);
                            $("#form_action").val(updateRoute);
                            $('#name').val(data.name);
                            $('#email').val(data.email);
                            $('#phone').val(data.phone);
                            $('#location').val(data.location);
                            $('#alternate_phone').val(data.alternate_phone);
                            $('#gst_number').val(data.gst_number);
                            $('#product_type').val(data.product_type);
                            $('#brand_name').val(data.brand_name);
                            $('#business_type option[value="' + data.business_type + '"]').prop(
                                'selected',
                                true);
                            $('#offcanvasFormLabel').html(
                                '<i class="bx bx-edit"></i> Edit Exhibitor');
                            $('#formSubmitBtn').html(
                                '<i class="bx bx-edit"></i> Update Exhibitor');
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



            // handle bulk upload data
            $(document).on('click', '#bulkImportBtn', function() {
                document.getElementById("bulkUploadForm").reset();
                $("#bulkUploadForm").validate().resetForm();
                bulkUploadFormCanvas.toggle();
            });


            $("#blkClancelBtn").on('click', function() {
                bulkUploadFormCanvas.toggle();
            });

            $("#bulkUploadForm").validate({
                errorClass: "text-danger",
                rules: {
                    file: {
                        required: true,
                        filesize: 104857600 // 100MB max file size, custom rule below
                    }
                },
                messages: {
                    file: {
                        required: "Please select a file to upload",
                        filesize: "File must be less than 100MB"
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var formData = new FormData(form);
                    formData.set('type', 'exhibitor');

                    $.ajax({
                        url: `{{ route('contact.import') }}`,
                        type: 'POST',
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status == true) {
                                dataTableList.ajax.reload();
                                toastr.success(response.message);
                                bulkUploadFormCanvas.toggle();
                            } else if (response.status == false) {
                                toastr.waring(response.message);
                            } else {
                                toastr.error('Upload failed. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Upload failed. Please try again.');
                        }
                    });
                }
            });

            // Custom file size validation
            $.validator.addMethod('filesize', function(value, element, param) {
                if (element.files.length === 0) {
                    return false;
                }
                return element.files[0].size <= param;
            }, 'File size must be less than {0} bytes');
        });
    </script>
@endsection
