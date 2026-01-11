@extends('layouts.app_layout')
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Locations</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role . '.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Locations</li>
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
                                    <th>Image</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Address</th>
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
                                    <i class="bx bx-user-circle"></i>&nbsp;Location Information
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="loc_name" class="form-label">Location Name <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="loc_name" name="loc_name"
                                            placeholder="Enter location name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="loc_address" name="address"
                                            placeholder="Enter address" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Type <span class="text-danger">*</span></label>
                                        <select name="type" id="type" class="form-select form-control" required>
                                            <option value="" selected disabled>Select Type</option>
                                            <option value="AC">AC</option>
                                            <option value="NON-AC">NON-AC</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Location Image (optional)</label>

                                        <div id="uploadWrapper">
                                            <input type="file" name="image" id="loc_image" class="form-control"
                                                accept="image/png, image/jpeg, image/jpg">
                                            <small class="text-muted"> JPG, JPEG & PNG allowed. Not mandatory. </small>
                                        </div>

                                        <div id="imagePreviewContainer" class="mt-2" style="display:none;">
                                            <label class="form-label fw-semibold">Current Image:</label><br>
                                            <a id="imagePreviewLink" href="#" target="_blank">
                                                <img id="imagePreview" src="" alt="Image Preview" width="100"
                                                    height="100"
                                                    style="border-radius:5px; object-fit:cover; border:1px solid #ccc;">
                                            </a>
                                        </div>
                                    </div>

                                </div>


                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Add Number of Tables</label>
                                        <div class="input-group">
                                            <input type="number" id="numTables" class="form-control" min="1"
                                                placeholder="Enter number of tables">
                                            <button type="button" id="generateTables"
                                                class="btn btn-primary">OK</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="tablesSection" class="mt-4" style="display:none;">
                                            <h5>Table Details</h5>
                                            <table class="table table-bordered align-middle text-center" id="tablesTable">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>SN</th>
                                                        <th>Table No</th>
                                                        <th>Size</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
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

            const dataTableList = $('#location_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollX: true,
                scrollCollapse: true,
                pagination: true,
                ajax: {
                    url: `${base_url}/admin/ajax/get/all-locations`,
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
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'loc_name',
                        name: 'loc_name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'address',
                        name: 'address'
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
                            class="lni lni-circle-plus mx-1"></i>Add New
                        Location</button>`
                );
            })




            // On click add button, open the modal
            $(document).on('click', '#addNewBtn', function() {
                document.getElementById("tableForm").reset();
                $("#tableForm").validate().resetForm();
                $('#operation_type').val('ADD');
                $("#form_action").val(`{{ route('locations.store') }}`);
                $("#hidden_id").val('');
                $('#offcanvasFormLabel').html('<i class="bx bx-plus"></i> Add New Location');
                $('#formSubmitBtn').html('<i class="bx bx-check-circle me-1"></i> Create Location');
                $("#status").prop('disabled', false);
                myOffcanvas.toggle();
            });

            // Filter functionality
            $('#filter_status').on('change', () => dataTableList.ajax.reload());





            // Add tabele rows dynamically
            document.getElementById('generateTables').addEventListener('click', function() {
                const num = document.getElementById('numTables').value;
                const section = document.getElementById('tablesSection');
                const tableBody = document.querySelector('#tablesTable tbody');
                tableBody.innerHTML = '';

                if (num > 0) {
                    section.style.display = 'block';
                    for (let i = 1; i <= num; i++) {
                        const row = `
                <tr>
                    <td>${i}</td>
                    <td><input type="text" name="tables[${i}][table_no]" class="form-control form-control-sm shadow-sm table_no" placeholder="Enter Table No."></td>
                    <td><input type="text" name="tables[${i}][table_size]" class="form-control form-control-sm shadow-sm table_size" placeholder="Enter Table Size"></td>
                    <td><input type="text" name="tables[${i}][price]" class="form-control form-control-sm shadow-sm price" placeholder="Enter Price"></td>
                </tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    }
                } else {
                    section.style.display = 'none';
                }
            });



            // jQuery Validation
            $("#tableForm").validate({
                errorClass: "text-danger validation-error",
                rules: {
                    loc_name: {
                        required: true,
                        minlength: 3
                    },
                    address: {
                        required: true,
                        minlength: 3

                    },
                    type: {
                        required: true,
                    },
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

                // Hide tables
                document.getElementById('tablesSection').style.display = 'none';
                document.querySelector('#tablesTable tbody').innerHTML = '';

                document.getElementById('imagePreviewContainer').style.display = 'none';
            }


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

                            console.log("data", data);

                            formReset();
                            $('#operation_type').val('EDIT');
                            $('#hidden_id').val(data.id);
                            $("#form_action").val(updateRoute);

                            // Fill main fields
                            $('#loc_name').val(data.loc_name);
                            $('#loc_address').val(data.address);
                            $('#type').val(data.type);
                            if (data.image) {
                                const imagePath = '/uploads/location_images/' + data.image;

                                document.getElementById('imagePreview').src = imagePath;
                                document.getElementById('imagePreviewLink').href = imagePath;
                                document.getElementById('imagePreviewContainer').style.display =
                                    'block';
                            } else {
                                document.getElementById('imagePreviewContainer').style.display =
                                    'none';
                            }

                            // ========== TABLE LOGIC FOR EDIT ==========
                            const tables = data.tables;
                            const numTables = tables.length;

                            // Set number of tables
                            document.getElementById('numTables').value = numTables;

                            const section = document.getElementById('tablesSection');
                            const tableBody = document.querySelector('#tablesTable tbody');

                            tableBody.innerHTML = ''; // clear old rows
                            section.style.display = 'block'; // show section

                            let i = 1;

                            tables.forEach(table => {
                                const row = `
                                    <tr>
                                        <td>${i}</td>
                                        <td><input type="text" name="tables[${i}][table_no]" value="${table.table_no}" class="form-control form-control-sm shadow-sm table_no"></td>
                                        <td><input type="text" name="tables[${i}][table_size]" value="${table.table_size}" class="form-control form-control-sm shadow-sm table_size"></td>
                                        <td><input type="text" name="tables[${i}][price]" value="${table.price}" class="form-control form-control-sm shadow-sm price"></td>

                                        <input type="hidden" name="tables[${i}][id]" value="${table.id}">
                                    </tr>
                                `;
                                tableBody.insertAdjacentHTML('beforeend', row);
                                i++;
                            });
                            // ===========================================

                            $('#offcanvasFormLabel').html(
                                '<i class="bx bx-edit"></i> Edit Location');
                            $('#formSubmitBtn').html(
                                '<i class="bx bx-edit"></i> Update Location');

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
                                url: base_url + `/admin/locations/${id}`,
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

            $("#cacnelBtn").on('click', function() {
                myOffcanvas.toggle();
            });

            $('#offcanvasForm').on('hidden.bs.offcanvas', function() {
                formReset();
            });

            // show detals in view modal
            $(document).on('click', '.showBtn', function() {
                const route = $(this).attr('editRoute');

                $.ajax({
                    url: route,
                    type: 'GET',
                    success: function(response) {
                        if (response.status == true) {

                            var data = response.data;

                            formReset();
                            $('#operation_type').val('SHOW'); // ---- IMPORTANT
                            $('#hidden_id').val(''); // ---- Not needed
                            $("#form_action").val(''); // ---- No API call needed

                            // Disable submit button
                            $('#formSubmitBtn').hide();


                            // Hide only upload input + text
                            $("#uploadWrapper").hide();

                            // Show preview
                            $("#imagePreviewContainer").show();

                            // Make all inputs readonly
                            $('#loc_name, #loc_address, #type, #numTables').prop('readonly',
                                true);
                            $('#type').prop('disabled', true);

                            // Set values
                            $('#loc_name').val(data.loc_name);
                            $('#loc_address').val(data.address);
                            $('#type').val(data.type);

                            // Show preview image only
                            if (data.image) {
                                const imagePath = '/uploads/location_images/' + data.image;

                                document.getElementById('imagePreview').src = imagePath;
                                document.getElementById('imagePreviewLink').href = imagePath;
                                document.getElementById('imagePreviewContainer').style.display =
                                    'block';
                            }

                            // --- TABLES (readonly) ---
                            const tables = data.tables;
                            const numTables = tables.length;

                            document.getElementById('numTables').value = numTables;

                            const section = document.getElementById('tablesSection');
                            const tableBody = document.querySelector('#tablesTable tbody');

                            tableBody.innerHTML = '';
                            section.style.display = 'block';

                            let i = 1;
                            tables.forEach(table => {
                                const row = `
                        <tr>
                            <td>${i}</td>
                            <td><input type="text" value="${table.table_no}" class="form-control form-control-sm table_no" readonly></td>
                            <td><input type="text" value="${table.table_size}" class="form-control form-control-sm table_size" readonly></td>
                            <td><input type="text" value="${table.price}" class="form-control form-control-sm price" readonly></td>
                        </tr>
                    `;
                                tableBody.insertAdjacentHTML('beforeend', row);
                                i++;
                            });

                            // Change header
                            $('#offcanvasFormLabel').html(
                                '<i class="bx bx-show"></i> View Location Details'
                            );

                            myOffcanvas.toggle();
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            });

        });
    </script>
@endsection
