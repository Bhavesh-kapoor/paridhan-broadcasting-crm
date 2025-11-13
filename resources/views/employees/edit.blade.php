@extends('layout.master')
@section('title', 'Edit Employee')

@section('content')
    <div class="row gy-4 mb-5">
        <div class="col-lg-12">
            <!-- Header Start -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center" style="padding:10px;">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark"> Edit Employee</h5>
                            <p class="text-muted mb-0">Update employee information</p>
                        </div>
                        <a href="{{ route('employees.index') }}"
                            class="py-12 text-15 px-20 hover-bg-gray-50 text-gray-300 rounded-8 flex-align gap-8 fw-medium text-15">
                            <i class="ph ph-arrow-left me-2"></i>&nbsp;Back to Employees
                        </a>
                    </div>
                </div>
            </div>
            <!-- Header End -->

            <!-- Form Start -->
            <div class="card mt-8">
                <div class="card-body p-4">
                    <form id="employeeForm" method="POST" action="{{ route('employees.update', $employee->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row" style="padding: 10px;">
                            <!-- Personal Information -->
                            <div class="col-lg-6">
                                <div class="p-3">
                                    <h6 class="mb-4 text-dark fw-semibold">
                                        <i class="ph ph-user-circle me-2"></i>&nbsp;Personal Information
                                    </h6>
                                    <hr>
                                    <div class="mb-3" style="margin-top: 10px;">
                                        <label for="name" class="form-label fw-semibold">
                                            <i class="ph ph-user me-2 text-muted"></i>&nbsp;Full Name <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $employee->name) }}"
                                            placeholder="Enter employee's full name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3" style="margin-top: 10px;">
                                        <label for="email" class="form-label fw-semibold">
                                            <i class="ph ph-envelope me-2 text-muted"></i>&nbsp;Email Address <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $employee->email) }}"
                                            placeholder="Enter email address" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3" style="margin-top: 10px;">
                                        <label for="phone" class="form-label fw-semibold">
                                            <i class="ph ph-phone me-2 text-muted"></i>&nbsp;Phone Number
                                        </label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" value="{{ old('phone', $employee->phone) }}"
                                            placeholder="Enter phone number">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3" style="margin-top: 10px;">
                                        <label for="date_of_birth" class="form-label fw-semibold">
                                            <i class="ph ph-calendar me-2 text-muted"></i>&nbsp;Date of Birth
                                        </label>
                                        <input type="date"
                                            class="form-control @error('date_of_birth') is-invalid @enderror"
                                            id="date_of_birth" name="date_of_birth"
                                            value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="col-lg-6">
                                <div class="p-3">
                                    <h6 class="mb-4 text-dark fw-semibold">
                                        <i class="ph ph-briefcase me-2"></i>&nbsp;Professional Information
                                    </h6>
                                    <hr>

                                    <div class="mb-3" style="margin-top: 10px;">
                                        <label for="position" class="form-label fw-semibold">
                                            <i class="ph ph-briefcase me-2 text-muted"></i>&nbsp;Position/Job Title
                                        </label>
                                        <input type="text" class="form-control @error('position') is-invalid @enderror"
                                            id="position" name="position"
                                            value="{{ old('position', $employee->position) }}"
                                            placeholder="Enter job position">
                                        @error('position')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3" style="margin-top: 10px;">
                                        <label for="status" class="form-label fw-semibold">
                                            <i class="ph ph-check-circle me-2 text-muted"></i>&nbsp;Status <span
                                                class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-control @error('status') is-invalid @enderror"
                                            id="status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="active"
                                                {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="inactive"
                                                {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="col-12 mt-6">
                                <div class="p-3">
                                    <h6 class="mb-4 text-dark fw-semibold">
                                        <i class="ph ph-map-pin me-2"></i>&nbsp;Address Information
                                    </h6>
                                    <hr>

                                    <div class="mb-3" style="margin-top: 10px;">
                                        <label for="address" class="form-label fw-semibold">
                                            <i class="ph ph-map-pin me-2 text-muted"></i>&nbsp;Address
                                        </label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2"
                                            placeholder="Enter full address">{{ old('address', $employee->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Security Information -->
                            <div class="col-12 mt-4">
                                <div class="p-3">
                                    <h6 class="mb-4 text-dark fw-semibold">
                                        <i class="ph ph-shield-check me-2"></i>&nbsp;Security Information
                                    </h6>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3" style="margin-top: 10px;">
                                                <label for="password" class="form-label fw-semibold">
                                                    <i class="ph ph-lock me-2 text-muted"></i>&nbsp;New Password
                                                </label>
                                                <div class="input-group">
                                                    <input type="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        id="password" name="password" placeholder="Enter new password">
                                                    <span class="input-group-text cursor-pointer" type="button"
                                                        onclick="togglePassword('password')">
                                                        <i class="ph ph-eye" id="password-icon"></i>&nbsp;
                                                    </span>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    <i class="ph ph-info me-1"></i>&nbsp;Leave blank to keep current
                                                    password. Must be at least 8 characters if changed.
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3" style="margin-top: 10px;">
                                                <label for="password_confirmation" class="form-label fw-semibold">
                                                    <i class="ph ph-lock-key me-2 text-muted"></i>&nbsp;Confirm New
                                                    Password
                                                </label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control"
                                                        id="password_confirmation" name="password_confirmation"
                                                        placeholder="Confirm new password">
                                                    <span class="input-group-text cursor-pointer" type="button"
                                                        onclick="togglePassword('password_confirmation')">
                                                        <i class="ph ph-eye" id="password_confirmation-icon"></i>&nbsp;
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4 mb-5">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mx-12">
                                    <button type="submit" class="btn btn-primary btn-sm shadow-sm" id="submitBtn">
                                        <i class="ph ph-check-circle me-2"></i>&nbsp;Update Employee
                                    </button>
                                    <a href="{{ route('employees.index') }}"
                                        class="py-12 text-15 px-20 hover-bg-danger-50 text-gray-300 hover-text-danger-600 rounded-8 flex-align gap-8 fw-medium text-15">
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
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'ph ph-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'ph ph-eye';
            }
        }

        $('#employeeForm').on('submit', function(e) {
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
