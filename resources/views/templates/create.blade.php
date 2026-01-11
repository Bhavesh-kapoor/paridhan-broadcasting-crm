@extends('layouts.app_layout')

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">WhatsApp Templates</div>
                <div class="ps-3">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route(Auth::user()->role . '.dashboard')}}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                        <li class="breadcrumb-item active">Create Template</li>
                    </ol>
                </div>

                <div class="ms-auto">
                    <a href="{{ route('templates.index') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="bx bx-list-ul"></i>&nbsp;All Templates
                    </a>
                </div>
            </div>

            <!-- Create Template Form -->
            <form id="templateForm" action="{{ route('templates.store') }}" method="POST">
                @csrf

                <div class="row mt-4">
                    <div class="col-lg-8 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="fw-semibold text-dark d-flex align-items-center mb-0 py-2">
                                    <i class="bx bx-message-square-add text-primary me-2"></i>Template Information
                                </h6>
                            </div>

                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="bx bx-check-circle me-2"></i>
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if(session('error') || $errors->has('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bx bx-error-circle me-2"></i>
                                        <strong>Error:</strong> {{ session('error') ?? $errors->first('error') }}
                                        @if(str_contains(strtolower(session('error') ?? $errors->first('error') ?? ''), 'already exists'))
                                            <div class="mt-2">
                                                <a href="{{ route('templates.index') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bx bx-list-ul"></i> View All Templates
                                                </a>
                                                <small class="d-block mt-2 text-muted">
                                                    <i class="bx bx-info-circle"></i> The template may already exist in the list. Please check the templates page or use a different name.
                                                </small>
                                            </div>
                                        @endif
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <i class="bx bx-error me-2"></i>
                                        <strong>Please fix the following errors:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Template Name <span class="text-danger">*</span>
                                            <i class="bx bx-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" 
                                               title="Template name must be unique, lowercase, and can only contain letters, numbers, and underscores. Once deleted, a name cannot be reused for 30 days."></i>
                                        </label>
                                        <input type="text" class="form-control" name="name" id="templateName" value="{{ old('name') }}"
                                            placeholder="e.g., welcome_message, order_confirmation_v2" required>
                                        <small class="text-muted">
                                            <i class="bx bx-check-circle"></i> Use lowercase letters, numbers, and underscores only. 
                                            <strong>Example:</strong> order_confirmation_v2, payment_reminder
                                        </small>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Language <span class="text-danger">*</span></label>
                                        <select class="form-select" name="language" required>
                                            <option value="en" selected>English</option>
                                            <option value="hi">Hindi</option>
                                            <option value="es">Spanish</option>
                                            <option value="fr">French</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Category <span class="text-danger">*</span>
                                            <i class="bx bx-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" 
                                               title="Choose the appropriate category for your template. Each category has specific use cases and approval requirements."></i>
                                        </label>
                                        <select class="form-select" name="category" id="categorySelect" required>
                                            <option value="">Select Category</option>
                                            <option value="MARKETING">Marketing</option>
                                            <option value="UTILITY">Utility</option>
                                            <option value="AUTHENTICATION">Authentication</option>
                                        </select>
                                        <div id="categoryHelp" class="mt-2"></div>
                                    </div>

                                    <div class="col-12">
                                        <hr class="my-3">
                                        <h6 class="fw-semibold">Header (Optional)</h6>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Header Type</label>
                                        <select class="form-select" name="header_type" id="headerType">
                                            <option value="">None</option>
                                            <option value="TEXT">Text</option>
                                            <option value="IMAGE">Image</option>
                                            <option value="VIDEO">Video</option>
                                            <option value="DOCUMENT">Document</option>
                                        </select>
                                    </div>

                                    <div class="col-md-8" id="headerTextDiv" style="display:none;">
                                        <label class="form-label">Header Text 
                                            <i class="bx bx-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" 
                                               title="Text headers support up to 60 characters. You can use variables like {{1}}, {{2}} in the header."></i>
                                        </label>
                                        <input type="text" class="form-control" name="header_text" id="headerText" 
                                            value="{{ old('header_text') }}" maxlength="60"
                                            placeholder="Enter header text (max 60 characters)">
                                        <small class="text-muted">Maximum 60 characters. Supports variables like {{1}}, {{2}}</small>
                                    </div>
                                    
                                    <div class="col-12 mt-2" id="headerMediaInfo" style="display:none;">
                                        <div class="alert alert-info mb-0">
                                            <i class="bx bx-info-circle"></i> <strong>Media Headers:</strong> For Image, Video, or Document headers, 
                                            you'll need to provide the media URL during template submission. Ensure the URL is publicly accessible via HTTPS.
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr class="my-3">
                                        <h6 class="fw-semibold">Body <span class="text-danger">*</span></h6>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Message Body <span class="text-danger">*</span>
                                            <i class="bx bx-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" 
                                               title="The main message content. Use {{1}}, {{2}}, {{3}} etc. for dynamic variables. Variables must be sequential and cannot skip numbers."></i>
                                        </label>
                                        <textarea class="form-control" name="body_text" id="bodyText" rows="6" required
                                            placeholder="Enter your message here. Use {{1}}, {{2}}, {{3}}, etc. for variables&#10;&#10;Example:&#10;Hello {{1}},&#10;Your order #{{2}} is confirmed.&#10;Total amount: {{3}}">{{ old('body_text') }}</textarea>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle"></i> Use <code>{{"{{"}}1</code>, <code>{{"{{"}}2</code>, <code>{{"{{"}}3</code> for dynamic variables. 
                                                Variables must be sequential (1, 2, 3...). Maximum 1024 characters.
                                            </small>
                                            <small class="text-muted" id="bodyCharCount">0 / 1024 characters</small>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr class="my-3">
                                        <h6 class="fw-semibold">Footer (Optional)</h6>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Footer Text (Optional)
                                            <i class="bx bx-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" 
                                               title="Footer text appears at the bottom of the message. Maximum 60 characters. Cannot contain variables."></i>
                                        </label>
                                        <input type="text" class="form-control" name="footer_text" id="footerText" 
                                            value="{{ old('footer_text') }}" maxlength="60"
                                            placeholder="e.g., Powered by Your Company, Contact Support">
                                        <small class="text-muted">Maximum 60 characters. Footer text cannot contain variables.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Important Information Card -->
                        <div class="card mt-3 border-info">
                            <div class="card-header bg-info bg-opacity-10">
                                <h6 class="fw-semibold mb-0 d-flex align-items-center">
                                    <i class="bx bx-info-circle text-info me-2"></i>
                                    Template Creation Guidelines
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0 small">
                                    <li><strong>Template Name:</strong> Must be unique, lowercase, alphanumeric with underscores only. Once deleted, cannot be reused for 30 days.</li>
                                    <li><strong>Body Text:</strong> Required, supports variables {{1}}, {{2}}, etc. Maximum 1024 characters. Variables must be sequential.</li>
                                    <li><strong>Header:</strong> Optional, maximum 60 characters for text. Media headers require publicly accessible HTTPS URLs.</li>
                                    <li><strong>Footer:</strong> Optional, maximum 60 characters. Cannot contain variables.</li>
                                    <li><strong>Approval Time:</strong> Templates are reviewed by Meta within 24-48 hours. AUTHENTICATION templates are usually approved faster.</li>
                                    <li><strong>Variables:</strong> Use {{1}}, {{2}}, {{3}} format. Must be sequential (1, 2, 3...). Cannot skip numbers.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card mt-3">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="submit" class="btn btn-primary d-flex align-items-center" id="submitBtn">
                                        <i class="bx bx-save"></i>&nbsp;Create Template
                                    </button>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bx bx-info-circle"></i> Template will be submitted for Meta review
                                    </small>
                                </div>

                                <a href="{{ route('templates.index') }}" class="btn btn-outline-danger d-flex align-items-center">
                                    <i class="bx bx-x"></i>&nbsp;Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Category descriptions
            const categoryInfo = {
                'MARKETING': {
                    title: '📢 Marketing Templates',
                    description: 'Use for promotional messages, sales announcements, product updates, and marketing campaigns.',
                    guidelines: [
                        'Allowed: Product promotions, sales, discounts, announcements',
                        '24-hour window: Recipients can respond within 24 hours of receiving',
                        'Cannot contain: Sensitive information, misleading content',
                        'Approval: Usually takes 24-48 hours for review'
                    ],
                    example: 'Promotional: "Get 50% off on all items! Use code {{1}} to avail."'
                },
                'UTILITY': {
                    title: '🔧 Utility Templates',
                    description: 'Use for transactional messages, updates, notifications, and customer service communications.',
                    guidelines: [
                        'Allowed: Order confirmations, shipping updates, account notifications',
                        'No promotional content: Must be purely transactional or informational',
                        '24-hour window: Recipients can respond within 24 hours',
                        'Approval: Usually faster approval than Marketing templates'
                    ],
                    example: 'Transactional: "Your order #{{1}} has been shipped. Track at {{2}}"'
                },
                'AUTHENTICATION': {
                    title: '🔐 Authentication Templates',
                    description: 'Use for OTP (One-Time Password), verification codes, login confirmations, and security alerts.',
                    guidelines: [
                        'Allowed: OTP codes, verification codes, login notifications, security alerts',
                        'Cannot contain: Promotional content, marketing messages',
                        'Variables: Must use variables for codes (e.g., {{1}} for OTP)',
                        'Approval: Fastest approval category (often auto-approved)'
                    ],
                    example: 'OTP: "Your verification code is {{1}}. Valid for 5 minutes."'
                }
            };

            // Show category information when selected
            $('#categorySelect').on('change', function() {
                const category = $(this).val();
                const helpDiv = $('#categoryHelp');
                
                if (category && categoryInfo[category]) {
                    const info = categoryInfo[category];
                    let html = `
                        <div class="alert alert-info mb-0">
                            <h6 class="fw-bold mb-2">${info.title}</h6>
                            <p class="mb-2">${info.description}</p>
                            <ul class="mb-2 small">
                    `;
                    info.guidelines.forEach(guideline => {
                        html += `<li>${guideline}</li>`;
                    });
                    html += `
                            </ul>
                            <div class="mt-2">
                                <strong>Example:</strong> <code class="small">${info.example}</code>
                            </div>
                        </div>
                    `;
                    helpDiv.html(html).show();
                } else {
                    helpDiv.html('').hide();
                }
            });

            // Show category info if already selected (e.g., from validation error)
            if ($('#categorySelect').val()) {
                $('#categorySelect').trigger('change');
            }

            // Show/hide header text input based on header type
            $('#headerType').on('change', function() {
                const headerType = $(this).val();
                const headerTextDiv = $('#headerTextDiv');
                const headerMediaInfo = $('#headerMediaInfo');
                
                if (headerType === 'TEXT') {
                    headerTextDiv.show();
                    headerMediaInfo.hide();
                } else if (headerType === 'IMAGE' || headerType === 'VIDEO' || headerType === 'DOCUMENT') {
                    headerTextDiv.hide();
                    headerMediaInfo.show();
                } else {
                    headerTextDiv.hide();
                    headerMediaInfo.hide();
                }
            });

            // Character counter for body text
            $('#bodyText').on('input', function() {
                const length = $(this).val().length;
                const maxLength = 1024;
                const counter = $('#bodyCharCount');
                
                counter.text(`${length} / ${maxLength} characters`);
                
                if (length > maxLength) {
                    counter.removeClass('text-muted').addClass('text-danger');
                } else if (length > maxLength * 0.9) {
                    counter.removeClass('text-muted text-danger').addClass('text-warning');
                } else {
                    counter.removeClass('text-warning text-danger').addClass('text-muted');
                }
            });

            // Initialize body text counter
            $('#bodyText').trigger('input');

            // Character counter for header text
            $('#headerText').on('input', function() {
                const length = $(this).val().length;
                const maxLength = 60;
                
                if (length > maxLength) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Character counter for footer text
            $('#footerText').on('input', function() {
                const length = $(this).val().length;
                const maxLength = 60;
                
                if (length > maxLength) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Template name validation and formatting
            $('#templateName').on('input', function() {
                let value = $(this).val();
                // Convert to lowercase and replace spaces/invalid chars with underscores
                value = value.toLowerCase().replace(/[^a-z0-9_]/g, '_');
                // Remove consecutive underscores
                value = value.replace(/_+/g, '_');
                // Remove leading/trailing underscores
                value = value.replace(/^_+|_+$/g, '');
                $(this).val(value);
                
                // Clear any previous error messages
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });
            
            // Check if template name already exists (optional - can be done via AJAX)
            // This is a client-side check, server will also validate

            // Form validation
            $('#templateForm').validate({
                errorClass: "text-danger validation-error",
                rules: {
                    name: {
                        required: true,
                        minlength: 3,
                        maxlength: 512,
                        pattern: /^[a-z0-9_]+$/
                    },
                    language: {
                        required: true
                    },
                    category: {
                        required: true
                    },
                    body_text: {
                        required: true,
                        minlength: 1,
                        maxlength: 1024
                    },
                    header_text: {
                        maxlength: 60
                    },
                    footer_text: {
                        maxlength: 60
                    }
                },
                messages: {
                    name: {
                        pattern: "Template name must contain only lowercase letters, numbers, and underscores",
                        minlength: "Template name must be at least 3 characters",
                        maxlength: "Template name cannot exceed 512 characters"
                    },
                    body_text: {
                        maxlength: "Body text cannot exceed 1024 characters"
                    },
                    header_text: {
                        maxlength: "Header text cannot exceed 60 characters"
                    },
                    footer_text: {
                        maxlength: "Footer text cannot exceed 60 characters"
                    }
                },
                submitHandler: function(form) {
                    // Validate variable numbering in body text
                    const bodyText = $('#bodyText').val();
                    const variablePattern = /\{\{(\d+)\}\}/g;
                    const variables = [];
                    let match;
                    
                    while ((match = variablePattern.exec(bodyText)) !== null) {
                        variables.push(parseInt(match[1]));
                    }
                    
                    if (variables.length > 0) {
                        // Check if variables are sequential starting from 1
                        const sortedVars = [...new Set(variables)].sort((a, b) => a - b);
                        const expectedVars = Array.from({length: sortedVars.length}, (_, i) => i + 1);
                        
                        if (JSON.stringify(sortedVars) !== JSON.stringify(expectedVars)) {
                            alert('Variables must be sequential starting from {{1}}. Example: {{1}}, {{2}}, {{3}}...');
                            return false;
                        }
                    }
                    
                    // Validate header text if header type is TEXT
                    const headerType = $('#headerType').val();
                    const headerText = $('#headerText').val();
                    if (headerType === 'TEXT' && (!headerText || headerText.trim() === '')) {
                        alert('Header text is required when header type is TEXT.');
                        $('#headerText').focus();
                        return false;
                    }
                    
                    $('#submitBtn').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i>&nbsp;Creating Template...');
                    form.submit();
                }
            });

            // Custom pattern validator
            $.validator.addMethod("pattern", function(value, element, pattern) {
                return this.optional(element) || pattern.test(value);
            }, "Invalid format");
        });
    </script>
@endsection
