@extends("layout.master")
@section('title','Change Employee Password')

@section('content')
<div class="row gy-4 mb-5">
    <div class="col-lg-12">
        <!-- Header Start -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Change Employee Password</h4>
                        <p class="text-muted mb-0">Update password for {{ $employee->name }}</p>
                    </div>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                        <i class="ph ph-arrow-left me-2"></i>Back to Employees
                    </a>
                </div>
            </div>
        </div>
        <!-- Header End -->

        <!-- Form Start -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <form id="changePasswordForm" method="POST" action="{{ route('employees.change-password.store', $employee->id) }}">
                            @csrf
                            
                            <!-- Employee Info -->
                            <div class="text-center mb-4">
                                <div class="avatar avatar-lg mx-auto mb-3">
                                    <div class="avatar-initial rounded-circle bg-primary text-white" style="width: 80px; height: 80px; font-size: 2rem;">
                                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                                    </div>
                                </div>
                                <h5 class="mb-1">{{ $employee->name }}</h5>
                                <p class="text-muted mb-0">{{ $employee->email }}</p>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-3 text-primary">
                                        <i class="ph ph-shield-check me-2"></i>Password Change
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                                   id="current_password" name="current_password" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                <i class="ph ph-eye" id="current_password-icon"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Enter the employee's current password to verify identity.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                                   id="new_password" name="new_password" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                                <i class="ph ph-eye" id="new_password-icon"></i>
                                            </button>
                                        </div>
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" 
                                                   id="new_password_confirmation" name="new_password_confirmation" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                                <i class="ph ph-eye" id="new_password_confirmation-icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                                            <i class="ph ph-x me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-warning" id="submitBtn">
                                            <i class="ph ph-key me-2"></i>Change Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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

$('#changePasswordForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $('#submitBtn');
    const originalText = submitBtn.html();
    
    submitBtn.prop('disabled', true);
    submitBtn.html('<i class="ph ph-spinner ph-spin me-2"></i>Changing Password...');
    
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
