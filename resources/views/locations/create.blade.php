@extends('layout.master')
@section('title', 'Create Campaign')

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
                                    <i class="ph ph-plus-circle me-3 text-primary"></i>Create New Location
                                </h5>
                                <p class="text-muted mb-0 fs-9">Create and configure your location</p>
                            </div>
                            <a href="{{ route('locations.index') }}" class="btn btn-secondary btn-lg shadow-sm">
                                <i class="ph ph-arrow-left me-2"></i>Back to locations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Form -->
        <div class="card">
            <div class="card-body">
                <form id="locationForm" class="mt-8">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Location Name <span class="text-danger">*</span></label>
                            <input type="text" name="loc_name" class="form-control form-control-lg shadow-sm"
                                placeholder="Enter location name" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control form-control-lg shadow-sm" rows="1" placeholder="Enter address"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select form-select-lg shadow-sm" required>
                                <option value="" selected disabled>Select Type</option>
                                <option value="AC">AC</option>
                                <option value="NON-AC">NON-AC</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-lg shadow-sm" required>
                                <option value="" selected disabled>Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Add Number of Tables</label>
                        <div class="input-group">
                            <input type="number" id="numTables" class="form-control" min="1"
                                placeholder="Enter number of tables">
                            <button type="button" id="generateTables" class="btn btn-primary">OK</button>
                        </div>
                    </div>

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

                    <div class="mt-8 text-center">
                        <button type="submit" class="btn btn-success">Save Location</button>
                    </div>
                </form>
            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <!-- SweetAlert2 CSS + JS -->

    <script>
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
                    <td><input type="text" name="tables[${i}][table_no]" class="form-control form-control-sm shadow-sm" placeholder="Enter Table No."></td>
                    <td><input type="text" name="tables[${i}][table_size]" class="form-control form-control-sm shadow-sm" placeholder="Enter Table Size"></td>
                    <td><input type="text" name="tables[${i}][price]" class="form-control form-control-sm shadow-sm" placeholder="Enter Price"></td>
                </tr>`;
                    tableBody.insertAdjacentHTML('beforeend', row);
                }
            } else {
                section.style.display = 'none';
            }
        });

        document.getElementById('locationForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            const response = await fetch('{{ route('locations.store') }}', {
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
                    title: 'Location Saved!',
                    text: 'Location and table details have been successfully added.',
                    showConfirmButton: false,
                    timer: 2000
                });
                this.reset();
                document.getElementById('tablesSection').style.display = 'none';
                setTimeout(() => {
                    window.location.href = "{{ route('locations.index') }}";
                }, 2000);

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while saving.',
                });
            }
        });
    </script>
@endpush
