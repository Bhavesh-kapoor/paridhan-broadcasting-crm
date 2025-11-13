@extends('layout.master')
@section('title', $title)

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-6">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center"
                            style="margin-left: 20px;padding:10px 2px">
                            <div>
                                <h5 class="mb-2 fw-bold text-dark">
                                    <i class="ph ph-pencil me-3 text-primary"></i>Edit Location - {{ $loc_name }}
                                </h5>
                                <p class="text-muted mb-0 fs-9">Modify location details and table information</p>
                            </div>
                            <a href="{{ route('locations.index') }}" class="btn btn-secondary btn-lg shadow-sm">
                                <i class="ph ph-arrow-left me-2"></i>Back to Locations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Location Form -->
        <div class="card">
            <div class="card-body">
                <form id="locationEditForm" class="mt-8">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" value="{{ $location->id }}">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Location Name <span class="text-danger">*</span></label>
                            <input type="text" name="loc_name" value="{{ $location->loc_name }}"
                                class="form-control form-control-lg shadow-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control form-control-lg shadow-sm" rows="1">{{ $location->address }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select form-select-lg shadow-sm" required>
                                <option value="AC" {{ $location->type == 'AC' ? 'selected' : '' }}>AC</option>
                                <option value="NON-AC" {{ $location->type == 'NON-AC' ? 'selected' : '' }}>NON-AC</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-lg shadow-sm" required>
                                <option value="active" {{ $location->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $location->status == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div id="tablesSection" class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold"><i class="ph ph-table me-2"></i>Table Details</h5>

                            <!-- Add more rows -->
                            <div class="input-group w-auto">
                                <input type="number" id="numTablesToAdd" class="form-control form-control-sm shadow-sm"
                                    min="1" placeholder="Add rows">
                                <button type="button" id="addTablesBtn" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="ph ph-plus-circle"></i> Add
                                </button>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle text-center" id="tablesTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>SN</th>
                                    <th>Table No</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($location->tables as $index => $table)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><input type="text" name="tables[{{ $index + 1 }}][table_no]"
                                                class="form-control form-control-sm shadow-sm"
                                                value="{{ $table->table_no }}"></td>
                                        <td><input type="text" name="tables[{{ $index + 1 }}][table_size]"
                                                class="form-control form-control-sm shadow-sm"
                                                value="{{ $table->table_size }}"></td>
                                        <td><input type="text" name="tables[{{ $index + 1 }}][price]"
                                                class="form-control form-control-sm shadow-sm" value="{{ $table->price }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 text-center">
                        <button type="submit" class="btn btn-success btn-lg shadow-sm">Update Location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addBtn = document.getElementById('addTablesBtn');
            const numInput = document.getElementById('numTablesToAdd');
            const tableBody = document.querySelector('#tablesTable tbody');

            addBtn.addEventListener('click', function() {
                const numToAdd = parseInt(numInput.value);
                if (!numToAdd || numToAdd <= 0) return;

                const currentRows = tableBody.querySelectorAll('tr').length;
                for (let i = 1; i <= numToAdd; i++) {
                    const sn = currentRows + i;
                    const newRow = `
                <tr>
                    <td>${sn}</td>
                    <td><input type="text" name="tables[${sn}][table_no]" class="form-control form-control-sm shadow-sm" placeholder="Enter Table No."></td>
                    <td><input type="text" name="tables[${sn}][table_size]" class="form-control form-control-sm shadow-sm" placeholder="Enter Table Size"></td>
                    <td><input type="text" name="tables[${sn}][price]" class="form-control form-control-sm shadow-sm" placeholder="Enter Price"></td>
                </tr>`;
                    tableBody.insertAdjacentHTML('beforeend', newRow);
                }

                numInput.value = ''; // clear input
            });
        });

        document.getElementById('locationEditForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const id = formData.get('id');

            const response = await fetch(`{{ url('locations') }}/${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                }
            });

            const result = await response.json();
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Location Updated!',
                    text: 'Location and table details have been successfully updated.',
                    showConfirmButton: false,
                    timer: 2000
                });

                setTimeout(() => {
                    window.location.href = "{{ route('locations.index') }}";
                }, 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while updating the location.',
                });
            }
        });
    </script>
@endpush --}}

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === Add Table Rows ===
            const addBtn = document.getElementById('addTablesBtn');
            const numInput = document.getElementById('numTablesToAdd');
            const tableBody = document.querySelector('#tablesTable tbody');

            addBtn.addEventListener('click', function() {
                const numToAdd = parseInt(numInput.value);
                if (!numToAdd || numToAdd <= 0) return;

                const currentRows = tableBody.querySelectorAll('tr').length;
                for (let i = 1; i <= numToAdd; i++) {
                    const sn = currentRows + i;
                    const newRow = `
                <tr>
                    <td>${sn}</td>
                    <td><input type="text" name="tables[${sn}][table_no]" class="form-control form-control-sm shadow-sm" placeholder="Enter Table No."></td>
                    <td><input type="text" name="tables[${sn}][table_size]" class="form-control form-control-sm shadow-sm" placeholder="Enter Table Size"></td>
                    <td><input type="text" name="tables[${sn}][price]" class="form-control form-control-sm shadow-sm" placeholder="Enter Price"></td>
                </tr>`;
                    tableBody.insertAdjacentHTML('beforeend', newRow);
                }

                numInput.value = ''; // clear input
            });

            // === AJAX Form Submit ===
            const form = document.getElementById('locationEditForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const id = formData.get('id');

                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait while we update the location.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`{{ url('admin/locations') }}/${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                            'X-HTTP-Method-Override': 'PUT'
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        Swal.close();

                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: result.message,
                                showConfirmButton: false,
                                timer: 2000
                            });

                            setTimeout(() => {
                                window.location.href = result.redirect;
                            }, 2000);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Something went wrong.'
                            });
                        }
                    })
                    .catch(() => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Server error while updating location.'
                        });
                    });
            });
        });
    </script>
@endpush
