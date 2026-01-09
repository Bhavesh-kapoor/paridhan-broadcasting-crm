@extends('layouts.app_layout')

@section('title', 'Table Availability & Campaigns')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/enhanced-tables.css') }}">
    <style>
        :root {
            --sidebar-start: #1e3a8a;
            --sidebar-end: #3b82f6;
        }
        
        .campaign-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
            border-left: 4px solid transparent;
            cursor: pointer;
        }
        
        .campaign-card:hover {
            border-left-color: var(--sidebar-end);
            box-shadow: 0 4px 16px rgba(30, 58, 138, 0.12);
            transform: translateY(-2px);
        }
        
        .campaign-card.active {
            border-left-color: var(--sidebar-end);
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
        }
        
        .table-card {
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        
        .table-card:hover {
            border-left-color: var(--sidebar-end);
            box-shadow: 0 4px 16px rgba(30, 58, 138, 0.12);
        }
        
        .table-item {
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .table-item.available {
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            border: 2px solid #86efac;
        }
        
        .table-item.used {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            border: 2px solid #475569;
        }
        
        .table-item.available:hover {
            background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%);
            transform: translateX(5px);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%);
            border: none;
            color: white;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.2);
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, var(--sidebar-end) 0%, var(--sidebar-start) 100%);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.4);
            transform: translateY(-2px);
            color: white;
        }
        
        .stat-badge {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
            color: var(--sidebar-end);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 8px;
            padding: 6px 12px;
            font-weight: 500;
        }
        
        .location-card-wrapper {
            animation: fadeInUp 0.4s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #locationSearch {
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 0.95rem;
        }
        
        #locationSearch:focus {
            border-color: var(--sidebar-end);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
        }
        
        .input-group-text {
            border-radius: 8px 0 0 8px;
        }
    </style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Table Availability & Campaigns</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active">Table Availability</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Campaigns Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
                            <h5 class="mb-0 fw-bold">
                                <i class="bx bx-megaphone me-2" style="color: var(--sidebar-end);"></i>Active Campaigns
                            </h5>
                            <small class="text-muted">Select a campaign to add conversations or view campaign details</small>
                        </div>
                        <div class="card-body">
                            @if($campaigns->count() > 0)
                                <div class="row g-3" id="campaignsList">
                                    @foreach($campaigns as $campaign)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card campaign-card" data-campaign-id="{{ $campaign->id }}" onclick="selectCampaign('{{ $campaign->id }}', '{{ $campaign->name }}')">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0 fw-semibold">{{ $campaign->name }}</h6>
                                                    <span class="badge bg-info">{{ strtoupper($campaign->status) }}</span>
                                                </div>
                                                <p class="text-muted small mb-2">{{ Str::limit($campaign->subject, 50) }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bx bx-calendar me-1"></i>{{ $campaign->created_at->format('M d, Y') }}
                                                    </small>
                                                    <a href="{{ route('campaigns.conversations', $campaign->id) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">
                                                        <i class="bx bx-conversation"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bx bx-megaphone fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No active campaigns found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Campaign Info -->
            <div class="row mb-4" id="selectedCampaignInfo" style="display: none;">
                <div class="col-12">
                    <div class="alert alert-info border-0 shadow-sm" style="background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%); border-left: 4px solid var(--sidebar-end); border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">
                                    <i class="bx bx-check-circle me-2"></i>Selected Campaign: <span id="selectedCampaignName"></span>
                                </h6>
                                <small class="text-muted">You can now add conversations or book tables for this campaign</small>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="clearCampaign()">
                                <i class="bx bx-x"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Search & Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
                            <h5 class="mb-0 fw-bold">
                                <i class="bx bx-search me-2" style="color: var(--sidebar-end);"></i>Location Search & Table Availability
                            </h5>
                            <small class="text-muted">Search and filter locations (handles millions of locations efficiently)</small>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bx bx-search text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control border-start-0" 
                                               id="locationSearch" 
                                               placeholder="Search locations by name... (supports 3M+ locations)">
                                    </div>
                                    <small class="text-muted">Type to search. Results load dynamically.</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2 align-items-end">
                                        <button class="btn btn-outline-secondary" id="clearSearch">
                                            <i class="bx bx-x"></i> Clear
                                        </button>
                                        <div class="flex-grow-1 text-end">
                                            <span class="badge bg-info" id="locationCount">Search to load locations</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Loading Indicator -->
                            <div id="locationsLoading" class="text-center py-4" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading locations...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading locations...</p>
                            </div>
                            
                            <!-- Locations Container -->
                            <div id="locationsContainer" class="row">
                                <!-- Locations will be loaded here via AJAX -->
                                <div class="col-12 text-center py-5">
                                    <i class="bx bx-map fs-1 text-muted mb-2"></i>
                                    <p class="text-muted">Start typing in the search box above to find locations</p>
                                </div>
                            </div>
                            
                            <!-- Load More Button -->
                            <div class="text-center mt-3" id="loadMoreContainer" style="display: none;">
                                <button class="btn btn-gradient" id="loadMoreBtn">
                                    <i class="bx bx-plus"></i> Load More Locations
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversation Modal -->
    <div class="modal fade" id="conversationModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title">
                        <i class="bx bx-conversation me-2"></i>Add Conversation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="conversationForm">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="campaign_id" id="form_campaign_id">
                        <input type="hidden" name="location_id" id="form_location_id">
                        <input type="hidden" name="table_id" id="form_table_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Exhibitor (Company) <span class="text-danger">*</span></label>
                                <select name="exhibitor_id" id="form_exhibitor_id" class="form-select" required>
                                    <option value="">-- Select Exhibitor --</option>
                                    @foreach($exhibitors as $exhibitor)
                                        <option value="{{ $exhibitor->id }}">{{ $exhibitor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Visitor/Lead</label>
                                <select name="visitor_id" id="form_visitor_id" class="form-select">
                                    <option value="">-- Select Visitor --</option>
                                    @foreach($visitors as $visitor)
                                        <option value="{{ $visitor->id }}">{{ $visitor->name }} ({{ $visitor->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Visitor Phone (if not in contacts)</label>
                                <input type="text" class="form-control" name="visitor_phone" id="form_visitor_phone" placeholder="Enter phone number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Outcome <span class="text-danger">*</span></label>
                                <select name="outcome" id="form_outcome" class="form-select" required>
                                    <option value="">-- Select Outcome --</option>
                                    <option value="busy">Busy</option>
                                    <option value="interested">Interested</option>
                                    <option value="materialised">Materialised</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Conversation Date</label>
                                <input type="datetime-local" class="form-control" name="conversation_date" id="form_conversation_date" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control" name="notes" id="form_notes" rows="3" placeholder="Enter conversation notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="bx bx-check me-1"></i>Add Conversation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title">
                        <i class="bx bx-calendar-check me-2"></i>Book Table
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="bookingForm">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="campaign_id" id="booking_campaign_id">
                        <input type="hidden" name="location_id" id="booking_location_id">
                        <input type="hidden" name="table_id" id="booking_table_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Exhibitor (Company) <span class="text-danger">*</span></label>
                                <select name="exhibitor_id" id="booking_exhibitor_id" class="form-select" required>
                                    <option value="">-- Select Exhibitor --</option>
                                    @foreach($exhibitors as $exhibitor)
                                        <option value="{{ $exhibitor->id }}">{{ $exhibitor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Visitor/Lead</label>
                                <select name="visitor_id" id="booking_visitor_id" class="form-select">
                                    <option value="">-- Select Visitor --</option>
                                    @foreach($visitors as $visitor)
                                        <option value="{{ $visitor->id }}">{{ $visitor->name }} ({{ $visitor->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone (if not in contacts)</label>
                                <input type="text" class="form-control" name="phone" id="booking_phone" placeholder="Enter phone number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Booking Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="booking_date" id="booking_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Total Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="price" id="booking_price" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Amount Paid</label>
                                <input type="number" step="0.01" class="form-control" name="amount_paid" id="booking_amount_paid" placeholder="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                                <select name="amount_status" id="booking_amount_status" class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="partial">Partial</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="bx bx-check me-1"></i>Book Table
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let selectedCampaignId = null;
        let selectedCampaignName = null;
        let currentPage = 1;
        let searchQuery = '';
        let hasMoreLocations = false;
        let searchTimeout = null;
        let debounceDelay = 500; // Debounce delay in milliseconds
        let pendingAjaxRequest = null; // Track pending AJAX request

        $(document).ready(function() {
            // Initialize location search
            initializeLocationSearch();
            
            // Load initial locations (first 12 locations) after a short delay for better UX
            setTimeout(function() {
                loadLocations(true);
            }, 300);
        });

        function initializeLocationSearch() {
            // Enhanced debounced search input with visual feedback
            $('#locationSearch').on('input', function() {
                const inputValue = $(this).val().trim();
                
                // Clear previous timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                    searchTimeout = null;
                }
                
                // Cancel any pending AJAX request
                if (pendingAjaxRequest) {
                    pendingAjaxRequest.abort();
                    pendingAjaxRequest = null;
                }
                
                // Show typing indicator if there's text
                if (inputValue.length > 0) {
                    $('#locationCount').html('<i class="bx bx-loader-alt bx-spin me-1"></i>Typing...');
                }
                
                // Update search query
                searchQuery = inputValue;
                currentPage = 1;
                
                // Set debounce timeout
                searchTimeout = setTimeout(function() {
                    searchTimeout = null;
                    
                    // Hide typing indicator
                    if (searchQuery.length > 0) {
                        $('#locationCount').html('<i class="bx bx-loader-alt bx-spin me-1"></i>Searching...');
                    }
                    
                    // Execute search
                    loadLocations(true);
                }, debounceDelay);
            });
            
            // Handle Enter key (immediate search without waiting for debounce)
            $('#locationSearch').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    
                    // Clear timeout and search immediately
                    if (searchTimeout) {
                        clearTimeout(searchTimeout);
                        searchTimeout = null;
                    }
                    
                    // Cancel pending request
                    if (pendingAjaxRequest) {
                        pendingAjaxRequest.abort();
                        pendingAjaxRequest = null;
                    }
                    
                    searchQuery = $(this).val().trim();
                    currentPage = 1;
                    $('#locationCount').html('<i class="bx bx-loader-alt bx-spin me-1"></i>Searching...');
                    loadLocations(true);
                }
            });
            
            // Clear search
            $('#clearSearch').on('click', function() {
                // Clear timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                    searchTimeout = null;
                }
                
                // Cancel pending request
                if (pendingAjaxRequest) {
                    pendingAjaxRequest.abort();
                    pendingAjaxRequest = null;
                }
                
                $('#locationSearch').val('');
                searchQuery = '';
                currentPage = 1;
                $('#locationCount').text('Search to load locations');
                loadLocations(true);
            });
            
            // Load more button
            $('#loadMoreBtn').on('click', function() {
                currentPage++;
                loadLocations(false);
            });
        }

        function loadLocations(replace = false) {
            if (replace) {
                $('#locationsContainer').empty();
                if (currentPage === 1) {
                    // Reset to first page only when replacing
                }
            }
            
            // Cancel any existing pending request
            if (pendingAjaxRequest) {
                pendingAjaxRequest.abort();
            }
            
            $('#locationsLoading').show();
            $('#loadMoreContainer').hide();
            
            // Store the AJAX request so we can cancel it if needed
            pendingAjaxRequest = $.ajax({
                url: '{{ route("table-availability.search-locations") }}',
                type: 'GET',
                data: {
                    search: searchQuery,
                    page: currentPage,
                    per_page: 12
                },
                success: function(response) {
                    // Clear pending request reference
                    pendingAjaxRequest = null;
                    $('#locationsLoading').hide();
                    
                    if (response.status) {
                        hasMoreLocations = response.has_more;
                        $('#locationCount').text(`${response.total} location(s) found`);
                        
                        if (response.data.length === 0 && replace) {
                            $('#locationsContainer').html(`
                                <div class="col-12 text-center py-5">
                                    <i class="bx bx-search fs-1 text-muted mb-2"></i>
                                    <p class="text-muted">No locations found matching "${searchQuery}"</p>
                                </div>
                            `);
                            return;
                        }
                        
                        // Append or replace locations (already cleared at start if replace)
                        response.data.forEach(function(location) {
                            // Check if location card already exists to avoid duplicates
                            if ($(`.location-card-wrapper[data-location-id="${location.id}"]`).length === 0) {
                                const locationCard = createLocationCard(location);
                                $('#locationsContainer').append(locationCard);
                                // Auto-load tables for this location
                                loadTables(location.id);
                            }
                        });
                        
                        // Show load more button if there are more results
                        if (hasMoreLocations) {
                            $('#loadMoreContainer').show();
                        } else {
                            $('#loadMoreContainer').hide();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // Clear pending request reference
                    pendingAjaxRequest = null;
                    
                    // Don't show error if request was aborted (cancelled intentionally)
                    if (status === 'abort') {
                        return;
                    }
                    
                    $('#locationsLoading').hide();
                    $('#locationCount').text('Error loading locations');
                    $('#locationsContainer').html(`
                        <div class="col-12 text-center py-5">
                            <i class="bx bx-error fs-1 text-danger mb-2"></i>
                            <p class="text-danger">Error loading locations. Please try again.</p>
                            <button class="btn btn-sm btn-primary mt-2" onclick="loadLocations(true)">
                                <i class="bx bx-refresh me-1"></i>Retry
                            </button>
                        </div>
                    `);
                }
            });
        }

        function createLocationCard(location) {
            return `
                <div class="col-lg-6 col-xl-4 mb-4 location-card-wrapper" data-location-id="${location.id}">
                    <div class="card table-card">
                        <div class="card-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-map me-2" style="color: var(--sidebar-end);"></i>${location.name}
                                    </h6>
                                    <small class="text-muted">${location.tables_count} table(s)</small>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" onclick="loadTables(${location.id})" title="Refresh Tables">
                                    <i class="bx bx-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="tables-${location.id}" class="mb-3" style="min-height: 100px;">
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <small class="d-block text-muted mt-2">Loading tables...</small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div>
                                    <span class="stat-badge me-2">
                                        <i class="bx bx-check-circle"></i> <span id="available-${location.id}">-</span> Available
                                    </span>
                                    <span class="stat-badge">
                                        <i class="bx bx-x-circle"></i> <span id="used-${location.id}">-</span> Used
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function selectCampaign(campaignId, campaignName) {
            selectedCampaignId = campaignId;
            selectedCampaignName = campaignName;
            
            // Update UI
            $('#campaignsList .campaign-card').removeClass('active');
            $(`#campaignsList .campaign-card[data-campaign-id="${campaignId}"]`).addClass('active');
            
            $('#selectedCampaignName').text(campaignName);
            $('#selectedCampaignInfo').fadeIn();
            
            // Update form fields
            $('#form_campaign_id').val(campaignId);
            $('#booking_campaign_id').val(campaignId);
        }

        function clearCampaign() {
            selectedCampaignId = null;
            selectedCampaignName = null;
            
            $('#campaignsList .campaign-card').removeClass('active');
            $('#selectedCampaignInfo').fadeOut();
            
            $('#form_campaign_id').val('');
            $('#booking_campaign_id').val('');
        }

        function loadTables(locationId) {
            $.ajax({
                url: `{{ route('table-availability.location', ':id') }}`.replace(':id', locationId),
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                        const container = $(`#tables-${locationId}`);
                        container.empty();
                        
                        $(`#available-${locationId}`).text(response.available_tables);
                        $(`#used-${locationId}`).text(response.used_tables);
                        
                        if (response.tables.length === 0) {
                            container.html('<div class="text-center text-muted py-3">No tables found</div>');
                            return;
                        }
                        
                        response.tables.forEach(function(table) {
                            const statusClass = table.is_used ? 'used' : 'available';
                            const statusIcon = table.is_used ? 'bx-x-circle' : 'bx-check-circle';
                            const statusText = table.is_used ? 'USED' : 'AVAILABLE';
                            
                            let info = '';
                            if (table.is_used) {
                                if (table.booking) {
                                    info = `<small class="d-block mt-1 opacity-75">Booked by: ${table.booking.visitor_name}</small>`;
                                } else if (table.conversation) {
                                    info = `<small class="d-block mt-1 opacity-75">In use: ${table.conversation.visitor_name}</small>`;
                                }
                            }
                            
                            const actions = table.is_used ? '' : `
                                <div class="mt-2 d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="openConversationModal(${locationId}, ${table.id})">
                                        <i class="bx bx-conversation"></i> Conversation
                                    </button>
                                    <button class="btn btn-sm btn-outline-success flex-fill" onclick="openBookingModal(${locationId}, ${table.id}, ${parseFloat(table.price || 0).toFixed(2)})">
                                        <i class="bx bx-calendar-check"></i> Book
                                    </button>
                                </div>
                            `;
                            
                            const item = `
                                <div class="table-item ${statusClass}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold">
                                                <i class="bx ${statusIcon} me-1"></i>Table ${table.table_no}
                                            </h6>
                                            <div class="d-flex gap-2 mb-1">
                                                <small><i class="bx bx-ruler"></i> ${table.table_size || 'N/A'}</small>
                                                <small><i class="bx bx-rupee"></i> ${parseFloat(table.price || 0).toFixed(2)}</small>
                                            </div>
                                            ${info}
                                            ${actions}
                                        </div>
                                        <span class="badge ${statusClass === 'used' ? 'bg-dark' : 'bg-success'}">
                                            ${statusText}
                                        </span>
                                    </div>
                                </div>
                            `;
                            container.append(item);
                        });
                    }
                },
                error: function() {
                    $(`#tables-${locationId}`).html('<div class="text-center text-danger py-3">Error loading tables</div>');
                }
            });
        }

        function openConversationModal(locationId, tableId) {
            $('#form_location_id').val(locationId);
            $('#form_table_id').val(tableId);
            if (selectedCampaignId) {
                $('#form_campaign_id').val(selectedCampaignId);
            }
            new bootstrap.Modal(document.getElementById('conversationModal')).show();
        }

        function openBookingModal(locationId, tableId, price) {
            $('#booking_location_id').val(locationId);
            $('#booking_table_id').val(tableId);
            $('#booking_price').val(price);
            if (selectedCampaignId) {
                $('#booking_campaign_id').val(selectedCampaignId);
            }
            new bootstrap.Modal(document.getElementById('bookingModal')).show();
        }

        // Handle conversation form submission
        $('#conversationForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '{{ route("table-availability.conversation") }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        bootstrap.Modal.getInstance(document.getElementById('conversationModal')).hide();
                        $('#conversationForm')[0].reset();
                        
                        // Reload tables
                        const locationId = $('#form_location_id').val();
                        loadTables(locationId);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors || {};
                    let errorMsg = 'Failed to create conversation';
                    if (Object.keys(errors).length > 0) {
                        errorMsg = Object.values(errors).flat().join(', ');
                    }
                    toastr.error(errorMsg);
                }
            });
        });

        // Handle booking form submission
        $('#bookingForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '{{ route("table-availability.booking") }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        bootstrap.Modal.getInstance(document.getElementById('bookingModal')).hide();
                        $('#bookingForm')[0].reset();
                        
                        // Reload tables
                        const locationId = $('#booking_location_id').val();
                        loadTables(locationId);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors || {};
                    let errorMsg = 'Failed to create booking';
                    if (Object.keys(errors).length > 0) {
                        errorMsg = Object.values(errors).flat().join(', ');
                    }
                    toastr.error(errorMsg);
                }
            });
        });
    </script>
@endsection
