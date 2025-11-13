@extends('layout.master')
@section('title', 'View location')

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
                                    <i class="ph ph-eye me-3 text-primary"></i>location Details
                                </h5>
                                <p class="text-muted mb-0 fs-9">View location information and recipients</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('locations.edit', $location->id) }}"
                                    class="btn btn-primary btn-lg shadow-sm">
                                    <i class="ph ph-pencil me-2"></i>Edit location
                                </a>
                                <a href="{{ route('locations.index') }}" class="btn btn-secondary btn-lg shadow-sm">
                                    <i class="ph ph-arrow-left me-2"></i>Back to locations
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm p-8 border-0 mt-8">

                    <h4 class="fw-bold mb-4 text-primary">
                        <i class="ph ph-map-pin-line me-2"></i> Location Details
                    </h4>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted">Location Name</label>
                            <input type="text" class="form-control form-control" value="{{ $location->loc_name }}"
                                readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted">Type</label>
                            <input type="text" class="form-control form-control" value="{{ $location->type }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Address</label>
                            <textarea class="form-control form-control" rows="1" readonly>{{ $location->address }}</textarea>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted">Status</label>
                            <div>
                                <span
                                    class="badge px-5 py-2 fs-6 bg-{{ $location->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($location->status) }}
                                </span>
                            </div>
                        </div>
                    </div>



                    <hr>

                    <h5>Table Details</h5>
                    <table class="table table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>SN</th>
                                <th>Table No</th>
                                <th>Size</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tableDetails as $index => $table)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $table->table_no }}</td>
                                    <td>{{ $table->table_size }}</td>
                                    <td>{{ $table->price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>




    </div>


@endsection

@push('scripts')
    <script></script>
    // You can add any JavaScript specific to this page here
    </script>
@endpush
