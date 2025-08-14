@extends("layout.master")
@section('title','Employee Details')

@section('content')
<div class="container-fluid">
    <!-- Enhanced Header Start -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg" >
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-white">
                            <h4 class="mb-2 fw-bold text-dark">
                                <i class="ph ph-user-circle me-2" style="font-size: 1.8rem;"></i>Employee Profile
                            </h4>
                            <p class="text-white-75 mb-0 fs-6">Comprehensive employee information and management</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm shadow-lg px-3 py-2" style="border-radius: 6px;">
                                <i class="ph ph-pencil me-1"></i>Edit Profile
                            </a>
                            <a href="{{ route('employees.change-password', $employee->id) }}" class="btn btn-warning btn-sm shadow-lg px-3 py-2" style="border-radius: 6px;">
                                <i class="ph ph-key me-1"></i>Change Password
                            </a>
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-light btn-sm px-3 py-2 border-2" style="border-radius: 6px;">
                                <i class="ph ph-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Enhanced Header End -->

    <div class="row g-4">
        <!-- Enhanced Employee Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <!-- Enhanced Profile Avatar -->
                    <div class="mb-4">
                        <div class="">
                            <div class="text-white d-flex align-items-center justify-content-center rounded-circle shadow-lg mx-auto" 
                                 style="width: 70px; height: 70px; font-size: 1.5rem; font-weight: 700; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);">
                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                            </div>
                            <div class="position-absolute top-0 start-0">
                                <div class="bg-white rounded-circle shadow-lg d-flex align-items-center justify-content-center" 
                                     style="width: 30px; height: 30px;">
                                    <i class="ph ph-crown text-warning" style="font-size: 0.9rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} rounded-pill px-3 py-2 fs-7 shadow-lg border-0 fw-bold">
                                <i class="ph ph-{{ $employee->status === 'active' ? 'check-circle' : 'pause-circle' }} me-1"></i>
                                {{ ucfirst($employee->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Enhanced Employee Info -->
                    <h5 class="mb-3 fw-bold text-dark">{{ $employee->name }}</h5>
                    <p class="text-muted mb-4 fs-6">
                        <i class="ph ph-briefcase me-1 text-primary"></i>
                        {{ $employee->position ?? 'No Position Assigned' }}
                    </p>
                    
                    <div class="bg-white rounded-3 p-3 mb-4 shadow-sm">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                         style="width: 35px; height: 35px;">
                                        <i class="ph ph-calendar text-primary fs-6"></i>
                                    </div>
                                    <small class="text-muted d-block fw-medium">Joined</small>
                                    <span class="fw-bold text-dark fs-7">{{ $employee->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                         style="width: 35px; height: 35px;">
                                        <i class="ph ph-clock text-info fs-6"></i>
                                    </div>
                                    <small class="text-muted d-block fw-medium">Last Updated</small>
                                    <span class="fw-bold text-dark fs-7">{{ $employee->updated_at->format('M d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                  
                </div>
            </div>
        </div>

        <!-- Enhanced Employee Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg h-100" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 py-4 text-center">
                    <h5 class="card-title mb-0 text-dark fw-bold">
                        <i class="ph ph-user-details me-2 text-primary" style="font-size: 1.2rem;"></i>Employee Information
                    </h5>
                </div>
                <div class="card-body p-4" style="margin-top:20px;">
                    <div class="row g-4">
                        <!-- Enhanced Personal Information -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="card-body p-4 text-center">
                                    <div class="d-flex align-items-center  mb-4">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="ph ph-user text-primary fs-6"></i>
                                        </div>
                                        <h6 class="text-dark mb-0 fw-bold">Personal Information</h6>
                                    </div>
                                    
                                    <div class="space-y-6">
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-primary border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Full Name</span>
                                            <span class="fw-bold text-dark fs-7">{{ $employee->name }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-primary border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Email Address</span>
                                            <span class="fw-bold text-dark fs-7">{{ $employee->email }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-primary border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Phone Number</span>
                                            <span class="fw-bold text-dark fs-7">{{ $employee->phone ?? 'Not provided' }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted fw-medium fs-7">Date of Birth</span>
                                            <span class="fw-bold text-dark fs-7">{{ $employee->date_of_birth ? $employee->date_of_birth->format('F d, Y') : 'Not provided' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Professional Information -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="card-body p-4 text-center">
                                    <div class="d-flex align-items-center justify-content-center mb-4">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="ph ph-briefcase text-success fs-6"></i>
                                        </div>
                                        <h6 class="text-dark mb-0 fw-bold">Professional Information</h6>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-success border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Position</span>
                                            <span class="fw-bold text-dark fs-7">{{ $employee->position ?? 'Not specified' }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-success border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Employee Since</span>
                                            <span class="fw-bold text-dark fs-7">{{ $employee->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-success border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Account Status</span>
                                            <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }} px-2 py-1 rounded-pill border-0 shadow-sm fs-7">
                                                <i class="ph ph-{{ $employee->status === 'active' ? 'check-circle' : 'pause-circle' }} me-1"></i>
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted fw-medium fs-7">Last Updated</span>
                                            <span class="fw-bold text-dark fs-7">{{ $employee->updated_at->format('M d, Y \a\t g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Address Information -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="card-body p-4 text-center">
                                    <div class="d-flex align-items-center justify-content-center mb-4">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="ph ph-map-pin text-info fs-6"></i>
                                        </div>
                                        <h6 class="text-dark mb-0 fw-bold">Address Information</h6>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center py-3">
                                        <span class="text-muted fw-medium fs-7">
                                            <i class="ph ph-map-pin me-1 text-info"></i>Address
                                        </span>
                                        <span class="fw-bold text-dark fs-7">{{ $employee->address ?? 'Not provided' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Quick Statistics Section -->
                        <div class="col-12">
                            <div class="card border-0 shadow-lg" style="border-radius: 15px;">
                                <div class="card-body p-4 text-center">
                                    <div class="d-flex align-items-center justify-content-center mb-4">
                                        <div class="bg-white bg-opacity-20 p-3 me-3">
                                            <i class="ph ph-chart-line text-dark fs-4"></i>
                                        </div>
                                        <h5 class="text-dark mb-0 fw-bold">Quick Statistics</h5>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="bg-white rounded-3 p-3 shadow-lg hover-zoom" style="transition: all 0.3s ease; cursor: pointer;">
                                                    <i class="ph ph-calendar-check text-success fs-3 mb-2"></i>
                                                    <h6 class="text-dark mb-1 fw-bold">{{ $employee->created_at->diffInDays(now()) }}</h6>
                                                    <small class="text-muted fw-medium fs-7">Days with us</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="bg-white rounded-3 p-3 shadow-lg hover-zoom" style="transition: all 0.3s ease; cursor: pointer;">
                                                    <i class="ph ph-clock text-info fs-3 mb-2"></i>
                                                    <h6 class="text-dark mb-1 fw-bold">{{ $employee->updated_at->diffInHours(now()) }}</h6>
                                                    <small class="text-muted fw-medium fs-7">Hours ago updated</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="bg-white rounded-3 p-3 shadow-lg hover-zoom" style="transition: all 0.3s ease; cursor: pointer;">
                                                    <i class="ph ph-user-circle text-primary fs-3 mb-2"></i>
                                                    <h6 class="text-dark mb-1 fw-bold">{{ strlen($employee->name) }}</h6>
                                                    <small class="text-muted fw-medium fs-7">Name length</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="bg-white rounded-3 p-3 shadow-lg hover-zoom" style="transition: all 0.3s ease; cursor: pointer;">
                                                    <i class="ph ph-envelope text-warning fs-3 mb-2"></i>
                                                    <h6 class="text-dark mb-1 fw-bold">{{ strlen($employee->email) }}</h6>
                                                    <small class="text-muted fw-medium fs-7">Email length</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white border-0" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="ph ph-warning me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-lg" 
                     style="width: 80px; height: 80px;">
                    <i class="ph ph-trash text-danger" style="font-size: 2rem;"></i>
                </div>
                <h5 class="mb-2 fw-bold text-dark">Are you absolutely sure?</h5>
                <p class="text-muted mb-0 fs-7">This action cannot be undone. The employee will be permanently removed from the system and all associated data will be lost.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-3">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4 py-2" style="border-radius: 6px;" data-bs-dismiss="modal">
                    <i class="ph ph-x me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger btn-sm px-4 py-2 shadow-lg" style="border-radius: 6px;" id="confirmDelete">
                    <i class="ph ph-trash me-1"></i>Delete Employee
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let employeeToDelete = null;

function deleteEmployee(employeeId) {
    employeeToDelete = employeeId;
    $('#deleteModal').modal('show');
}

function toggleStatus(employeeId) {
    $.ajax({
        url: `/admin/employees/${employeeId}/toggle-status`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showNotification('error', response.message || 'Something went wrong!');
        }
    });
}

$('#confirmDelete').click(function() {
    if (employeeToDelete) {
        $.ajax({
            url: `/admin/employees/${employeeToDelete}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    showNotification('success', response.message);
                    setTimeout(() => {
                        window.location.href = '{{ route("employees.index") }}';
                    }, 1000);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showNotification('error', response.message || 'Something went wrong!');
            }
        });
    }
});

// Add hover zoom effect for statistics cards
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.hover-zoom');
    
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
        });
    });
});

// Notification function is now available globally from master layout
</script>

<style>
.hover-zoom {
    transition: all 0.3s ease;
}

.hover-zoom:hover {
    transform: scale(1.05);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2) !important;
}
</style>
@endpush
