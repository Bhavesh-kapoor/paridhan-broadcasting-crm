@extends("layout.master")
@section('title', $title)

@section('content')
<div class="container-fluid">
    <!-- Header Start -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-white">
                            <h4 class="mb-2 fw-bold text-dark">
                                <i class="ph ph-{{ $contact->type === 'exhibitor' ? 'storefront' : 'user-circle' }} me-2" style="font-size: 1.8rem;"></i>{{ $title }}
                            </h4>
                            <p class="text-white-75 mb-0 fs-6">View {{ $contact->type }} information and details</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-warning btn-sm shadow-lg px-3 py-2" style="border-radius: 6px;">
                                <i class="ph ph-pencil me-1"></i>Edit {{ ucfirst($contact->type) }}
                            </a>
                            <a href="{{ route('contacts.index', ['type' => $contact->type]) }}" class="btn btn-outline-secondary btn-sm px-3 py-2 border-2" style="border-radius: 6px;">
                                <i class="ph ph-arrow-left me-1"></i>Back to {{ ucfirst($contact->type) }}s
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <div class="row g-4">
        <!-- Contact Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <!-- Profile Avatar -->
                    <div class="mb-4">
                        <div class="text-white d-flex align-items-center justify-content-center rounded-circle shadow-lg mx-auto" 
                             style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: 700; background: linear-gradient(135deg, {{ $contact->type === 'exhibitor' ? '#667eea' : '#28a745' }} 0%, {{ $contact->type === 'exhibitor' ? '#764ba2' : '#20c997' }} 100%);">
                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-{{ $contact->type === 'exhibitor' ? 'primary' : 'success' }} rounded-pill px-3 py-2 fs-7 shadow-lg border-0 fw-bold">
                                <i class="ph ph-{{ $contact->type === 'exhibitor' ? 'storefront' : 'user-circle' }} me-1"></i>
                                {{ $contact->type_display_name }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Contact Info -->
                    <h5 class="mb-3 fw-bold text-dark">{{ $contact->name }}</h5>
                    <p class="text-muted mb-4 fs-6">
                        <i class="ph ph-map-pin me-1 text-{{ $contact->type === 'exhibitor' ? 'primary' : 'success' }}"></i>
                        {{ $contact->location }}
                    </p>
                    
                    <!-- Quick Stats -->
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="bg-{{ $contact->type === 'exhibitor' ? 'primary' : 'success' }} bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-1" 
                                         style="width: 35px; height: 35px;">
                                        <i class="ph ph-calendar text-{{ $contact->type === 'exhibitor' ? 'primary' : 'success' }} fs-6"></i>
                                    </div>
                                    <small class="text-muted d-block fw-medium">Added</small>
                                    <span class="fw-bold text-dark fs-7">{{ $contact->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-1" 
                                         style="width: 35px; height: 35px;">
                                        <i class="ph ph-clock text-info fs-6"></i>
                                    </div>
                                    <small class="text-muted d-block fw-medium">Updated</small>
                                    <span class="fw-bold text-dark fs-7">{{ $contact->updated_at->format('M d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg h-100" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="card-title mb-0 text-dark fw-bold text-center">
                        <i class="ph ph-user-details me-2 text-primary" style="font-size: 1.2rem;"></i>{{ ucfirst($contact->type) }} Information
                    </h5>
                </div>
                <div class="card-body p-4" style="margin-top:20px;">
                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="card-body p-4 text-center">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="ph ph-user text-primary fs-6"></i>
                                        </div>
                                        <h6 class="text-dark mb-0 fw-bold">Basic Information</h6>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-primary border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Full Name</span>
                                            <span class="fw-bold text-dark fs-7">{{ $contact->name }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-primary border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Location</span>
                                            <span class="fw-bold text-dark fs-7">{{ $contact->location }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted fw-medium fs-7">Contact Type</span>
                                            <span class="badge bg-{{ $contact->type === 'exhibitor' ? 'primary' : 'success' }} px-2 py-1 rounded-pill border-0 shadow-sm fs-7">
                                                {{ $contact->type_display_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="card-body p-4 text-center">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="ph ph-phone text-success fs-6"></i>
                                        </div>
                                        <h6 class="text-dark mb-0 fw-bold">Contact Information</h6>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-success border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Phone Number</span>
                                            <span class="fw-bold text-dark fs-7">{{ $contact->phone }}</span>
                                        </div>
                                        
                                        @if($contact->alternate_phone)
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-success border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Alternate Phone</span>
                                            <span class="fw-bold text-dark fs-7">{{ $contact->alternate_phone }}</span>
                                        </div>
                                        @endif
                                        
                                        @if($contact->email)
                                        <div class="d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted fw-medium fs-7">Email Address</span>
                                            <span class="fw-bold text-dark fs-7">{{ $contact->email }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($contact->isExhibitor())
                        <!-- Exhibitor Specific Information -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="card-body p-4 text-center">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="ph ph-package text-info fs-6"></i>
                                        </div>
                                        <h6 class="text-dark mb-0 fw-bold">Product Information</h6>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-info border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Product Type</span>
                                            <span class="fw-bold text-dark fs-7">{{ $contact->product_type }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted fw-medium fs-7">Brand Name</span>
                                            <span class="fw-bold text-dark fs-7">{{ $contact->brand_name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="card-body p-4 text-center">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="ph ph-buildings text-warning fs-6"></i>
                                        </div>
                                        <h6 class="text-dark mb-0 fw-bold">Business Information</h6>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-warning border-opacity-25">
                                            <span class="text-muted fw-medium fs-7">Business Type</span>
                                            <span class="fw-bold text-dark fs-7">{{ $contact->business_type }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted fw-medium fs-7">GST Number</span>
                                            <span class="fw-bold text-dark fs-7">{{ $contact->gst_number }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Timestamps -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="card-body p-4 text-center">
                                    <div class="d-flex align-items-center justify-content-center mb-4">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="ph ph-clock text-secondary fs-6"></i>
                                        </div>
                                        <h6 class="text-dark mb-0 fw-bold">Timestamps</h6>
                                    </div>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="text-center">
                                                <small class="text-muted d-block fw-medium">Created</small>
                                                <span class="fw-bold text-dark fs-7">{{ $contact->created_at->format('F d, Y \a\t g:i A') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-center">
                                                <small class="text-muted d-block fw-medium">Last Updated</small>
                                                <span class="fw-bold text-dark fs-7">{{ $contact->updated_at->format('F d, Y \a\t g:i A') }}</span>
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
@endsection
