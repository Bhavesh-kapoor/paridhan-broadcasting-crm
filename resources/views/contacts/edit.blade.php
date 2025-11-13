@extends("layout.master")
@section('title', $title)

@section('content')
<div class="row gy-4 mb-5">
    <div class="col-lg-12">
        <!-- Header Start -->
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center" style="padding:10px;">
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">{{ $title }}</h5>
                        <p class="text-muted mb-0">Update {{ $type }} information</p>
                    </div>
                    <a href="{{ route('contacts.index', ['type' => $type]) }}" class="py-12 text-15 px-20 hover-bg-gray-50 text-gray-300 rounded-8 flex-align gap-8 fw-medium text-15">
                        <i class="ph ph-arrow-left me-2"></i>&nbsp;Back to {{ ucfirst($type) }}s
                    </a>
                </div>
            </div>
        </div>
        <!-- Header End -->

        <!-- Form Start -->
        <div class="card mt-8">
            <div class="card-body p-4">
                <form id="contactForm" method="POST" action="{{ route('contacts.update', $contact->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div class="row" style="padding: 10px;">
                        <!-- Basic Information -->
                        <div class="col-lg-6">
                            <div class="p-3">
                                <h6 class="mb-4 text-dark fw-semibold">
                                    <i class="ph ph-user-circle me-2"></i>&nbsp;Basic Information
                                </h6>
                                <hr>
                                <div class="mb-3" style="margin-top: 10px;">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="ph ph-user me-2 text-muted"></i>&nbsp;Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $contact->name) }}"
                                           placeholder="Enter {{ $type }}'s full name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" style="margin-top: 10px;">
                                    <label for="location" class="form-label fw-semibold">
                                        <i class="ph ph-map-pin me-2 text-muted"></i>&nbsp;Location <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                           id="location" name="location" value="{{ old('location', $contact->location) }}"
                                           placeholder="Enter location" required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-lg-6">
                            <div class="p-3">
                                <h6 class="mb-4 text-dark fw-semibold">
                                    <i class="ph ph-phone me-2"></i>&nbsp;Contact Information
                                </h6>
                                <hr>

                                <div class="mb-3" style="margin-top: 10px;">
                                    <label for="phone" class="form-label fw-semibold">
                                        <i class="ph ph-phone me-2 text-muted"></i>&nbsp;Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', $contact->phone) }}"
                                           placeholder="Enter phone number" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($type === 'exhibitor')
                                <div class="mb-3" style="margin-top: 10px;">
                                    <label for="alternate_phone" class="form-label fw-semibold">
                                        <i class="ph ph-phone me-2 text-muted"></i>&nbsp;Alternate Phone
                                    </label>
                                    <input type="text" class="form-control @error('alternate_phone') is-invalid @enderror"
                                           id="alternate_phone" name="alternate_phone" value="{{ old('alternate_phone', $contact->alternate_phone) }}"
                                           placeholder="Enter alternate phone">
                                    @error('alternate_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($type === 'exhibitor')
                        <!-- Business Information -->
                        <div class="col-12 mt-4">
                            <div class="p-3">
                                <h6 class="mb-4 text-dark fw-semibold">
                                    <i class="ph ph-storefront me-2"></i>&nbsp;Business Information
                                </h6>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3" style="margin-top: 10px;">
                                            <label for="email" class="form-label fw-semibold">
                                                <i class="ph ph-envelope me-2 text-muted"></i>&nbsp;Email Address <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email', $contact->email) }}"
                                                   placeholder="Enter email address" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3" style="margin-top: 10px;">
                                            <label for="gst_number" class="form-label fw-semibold">
                                                <i class="ph ph-receipt me-2 text-muted"></i>&nbsp;GST Number
                                            </label>
                                            <input type="text" class="form-control @error('gst_number') is-invalid @enderror"
                                                   id="gst_number" name="gst_number" value="{{ old('gst_number', $contact->gst_number) }}"
                                                   placeholder="Enter GST number">
                                            @error('gst_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3" style="margin-top: 10px;">
                                            <label for="product_type" class="form-label fw-semibold">
                                                <i class="ph ph-package me-2 text-muted"></i>&nbsp;Product Type
                                            </label>
                                            <input type="text" class="form-control @error('product_type') is-invalid @enderror"
                                                   id="product_type" name="product_type" value="{{ old('product_type', $contact->product_type) }}"
                                                   placeholder="Enter product type">
                                            @error('product_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3" style="margin-top: 10px;">
                                            <label for="brand_name" class="form-label fw-semibold">
                                                <i class="ph ph-tag me-2 text-muted"></i>&nbsp;Brand Name
                                            </label>
                                            <input type="text" class="form-control @error('brand_name') is-invalid @enderror"
                                                   id="brand_name" name="brand_name" value="{{ old('brand_name', $contact->brand_name) }}"
                                                   placeholder="Enter brand name">
                                            @error('brand_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3" style="margin-top: 10px;">
                                            <label for="business_type" class="form-label fw-semibold">
                                                <i class="ph ph-buildings me-2 text-muted"></i>&nbsp;Business Type
                                            </label>
                                            <select class="form-select form-control @error('business_type') is-invalid @enderror"
                                                    id="business_type" name="business_type">
                                                <option value="">Select business type</option>
                                                <option value="Manufacturer" {{ old('business_type', $contact->business_type) == 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                                <option value="Distributor" {{ old('business_type', $contact->business_type) == 'Distributor' ? 'selected' : '' }}>Distributor</option>
                                                <option value="Retailer" {{ old('business_type', $contact->business_type) == 'Retailer' ? 'selected' : '' }}>Retailer</option>
                                                <option value="Wholesaler" {{ old('business_type', $contact->business_type) == 'Wholesaler' ? 'selected' : '' }}>Wholesaler</option>
                                                <option value="Service Provider" {{ old('business_type', $contact->business_type) == 'Service Provider' ? 'selected' : '' }}>Service Provider</option>
                                                <option value="Other" {{ old('business_type', $contact->business_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('business_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="row mt-4 mb-5">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center  mx-12">
                                <button type="submit" class="btn btn-{{ $type === 'exhibitor' ? 'primary' : 'success' }} btn-sm shadow-sm" id="submitBtn">
                                    <i class="ph ph-check-circle me-2"></i>&nbsp;Update {{ ucfirst($type) }}
                                </button>
                                <a href="{{ route('contacts.index', ['type' => $type]) }}" class="py-12 text-15 px-20 hover-bg-danger-50 text-gray-300 hover-text-danger-600 rounded-8 flex-align gap-8 fw-medium text-15">
                                    <i class="ph ph-x me-2"></i>&nbsp;Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Form End -->
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-format phone numbers
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) value = value.slice(0, 10);
    e.target.value = value;
});

if (document.getElementById('alternate_phone')) {
    document.getElementById('alternate_phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) value = value.slice(0, 10);
        e.target.value = value;
    });
}

// Auto-format GST number
if (document.getElementById('gst_number')) {
    document.getElementById('gst_number').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 15) value = value.slice(0, 15);
        e.target.value = value;
    });
}

$('#contactForm').on('submit', function(e) {
    e.preventDefault();

    const submitBtn = $('#submitBtn');
    const originalText = submitBtn.html();

    submitBtn.prop('disabled', true);
    submitBtn.html('<i class="ph ph-spinner ph-spin me-2"></i>&nbsp;Updating...');

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1000);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response.errors) {
                // Handle validation errors
                Object.keys(response.errors).forEach(field => {
                    const input = $(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(response.errors[field][0]);
                });
            } else {
                showNotification('error', response.message || 'Something went wrong!');
            }
        },
        complete: function() {
            submitBtn.prop('disabled', false);
            submitBtn.html(originalText);
        }
    });
});

// Notification function is now available globally from master layout
</script>
@endpush
