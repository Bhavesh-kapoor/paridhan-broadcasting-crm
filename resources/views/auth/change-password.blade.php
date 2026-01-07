@extends("layout.master")
@section('title','Change Password')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center" style="margin-left: 20px;padding:10px 2px">
                        <div>
                            <h5 class="mb-2 fw-bold text-dark">
                                <i class="ph ph-lock-key me-3 text-primary"></i>Change Password
                            </h5>
                            <p class="text-muted mb-0 fs-9">Update your account password</p>
                        </div>
                        <a href="{{ route(Auth::user()->role . '.dashboard')}}" class="btn btn-secondary btn-lg shadow-sm">
                            <i class="ph ph-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Form -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm" style="padding: 20px !important;">

                <div class="card-body p-4">
                    <form id="changePasswordForm" action="{{ route('admin.change-password.store') }}" method="POST">
                        @csrf

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="ph ph-lock me-2 text-muted"></i>Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="current_password"
                                       id="current_password" placeholder="Enter your current password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="ph ph-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="ph ph-lock-key me-2 text-muted"></i>New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="new_password"
                                       id="new_password" placeholder="Enter your new password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="ph ph-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <i class="ph ph-info me-1"></i>Password must be at least 8 characters long
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="ph ph-lock-key me-2 text-muted"></i>Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="new_password_confirmation"
                                       id="new_password_confirmation" placeholder="Confirm your new password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="ph ph-eye" id="new_password_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Password Requirements -->
                        <div class="mb-4">
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <h6 class="fw-semibold text-dark mb-2">
                                        <i class="ph ph-check-circle me-2 text-primary"></i>Password Requirements
                                    </h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li class="mb-1">
                                            <i class="ph ph-check text-success me-2"></i>At least 8 characters long
                                        </li>
                                        <li class="mb-1">
                                            <i class="ph ph-check text-success me-2"></i>Should contain uppercase and lowercase letters
                                        </li>
                                        <li class="mb-1">
                                            <i class="ph ph-check text-success me-2"></i>Should contain numbers
                                        </li>
                                        <li class="mb-0">
                                            <i class="ph ph-check text-success me-2"></i>Should contain special characters
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="ph ph-check-circle me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '_icon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ph ph-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'ph ph-eye';
    }
}

// Form submission
$('#changePasswordForm').on('submit', function(e) {
    e.preventDefault();

    // Show loading state
    $('#submitBtn').prop('disabled', true).html('<i class="ph ph-spinner me-2"></i>Updating...');

    // Submit form via AJAX
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
                // Reset form
                $('#changePasswordForm')[0].reset();
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response.errors) {
                let errorMessage = 'Please fix the following errors:\n';
                Object.keys(response.errors).forEach(key => {
                    errorMessage += `• ${response.errors[key][0]}\n`;
                });
                showNotification('error', errorMessage);
            } else {
                showNotification('error', response.message || 'Something went wrong!');
            }
        },
        complete: function() {
            // Reset loading state
            $('#submitBtn').prop('disabled', false).html('<i class="ph ph-check-circle me-2"></i>Update Password');
        }
    });
});
</script>
@endpush
