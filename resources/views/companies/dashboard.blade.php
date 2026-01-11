@extends('layouts.app_layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('/assets/css/enhanced-tables.css') }}">
    <style>
        .timeline-item {
            border-left: 3px solid #667eea;
            padding-left: 20px;
            margin-bottom: 30px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            width: 12px;
            height: 12px;
            background: #667eea;
            border-radius: 50%;
            position: absolute;
            left: -7.5px;
            top: 5px;
        }
        .timeline-item.materialised {
            border-left-color: #198754;
        }
        .timeline-item.materialised::before {
            background: #198754;
        }
        .timeline-item.busy {
            border-left-color: #ffc107;
        }
        .timeline-item.busy::before {
            background: #ffc107;
        }
        .timeline-item.interested {
            border-left-color: #0dcaf0;
        }
        .timeline-item.interested::before {
            background: #0dcaf0;
        }
    </style>
@endsection
@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-2">
                <div class="breadcrumb-title pe-3">Company Dashboard</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role . '.dashboard')}}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('contacts.index', ['type' => 'exhibitor']) }}">Exhibitors</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $exhibitor->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Company Info Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <h2 class="text-white mb-2 fw-bold">
                                        <i class="bx bx-store-alt me-2"></i>{{ $exhibitor->name }}
                                    </h2>
                                    <p class="text-white mb-1 opacity-90">
                                        {{ $exhibitor->email ?? 'No email' }} | {{ $exhibitor->phone ?? 'No phone' }}
                                    </p>
                                    @if($exhibitor->location)
                                    <div class="text-white opacity-75">
                                        <i class="bx bx-map me-1"></i>{{ $exhibitor->location }}
                                    </div>
                                    @endif
                                </div>
                                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                                    <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center p-4">
                                        <i class="bx bx-line-chart text-white" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <div class="mb-2">
                                <i class="bx bx-user-plus fs-1 text-primary"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-primary">{{ $dashboard['total_leads'] ?? 0 }}</h3>
                            <p class="mb-0 text-muted small">Total Leads</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <div class="mb-2">
                                <i class="bx bx-check-circle fs-1 text-success"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-success">{{ $dashboard['total_bookings'] ?? 0 }}</h3>
                            <p class="mb-0 text-muted small">Total Bookings</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <div class="mb-2">
                                <i class="bx bx-rupee fs-1 text-purple" style="color: #764ba2;"></i>
                            </div>
                            <h3 class="fw-bold mb-1" style="color: #764ba2;">₹{{ number_format($dashboard['total_revenue'] ?? 0, 2) }}</h3>
                            <p class="mb-0 text-muted small">Total Revenue</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <div class="mb-2">
                                <i class="bx bx-conversation fs-1 text-info"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-info">{{ count($dashboard['recent_conversations'] ?? []) }}</h3>
                            <p class="mb-0 text-muted small">Recent Conversations</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contributing Campaigns -->
            @if(isset($dashboard['contributing_campaigns']) && count($dashboard['contributing_campaigns']) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="bx bx-megaphone me-2 text-primary"></i>Contributing Campaigns
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th><i class="bx bx-rename"></i> Campaign Name</th>
                                            <th class="text-center"><i class="bx bx-check-circle"></i> Bookings</th>
                                            <th class="text-end"><i class="bx bx-rupee"></i> Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dashboard['contributing_campaigns'] as $campaign)
                                        <tr>
                                            <td>
                                                <a href="{{ route('campaigns.show', $campaign['id']) }}" class="text-decoration-none">
                                                    {{ $campaign['name'] }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $campaign['bookings_count'] }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-success">
                                                ₹{{ number_format($campaign['revenue'], 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Stall Performance -->
            @if(isset($dashboard['stall_performance']) && count($dashboard['stall_performance']) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="bx bx-table me-2 text-primary"></i>Stall-wise Performance
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th><i class="bx bx-map"></i> Location</th>
                                            <th><i class="bx bx-table"></i> Table/Stall</th>
                                            <th class="text-center"><i class="bx bx-conversation"></i> Conversations</th>
                                            <th class="text-center"><i class="bx bx-check-circle"></i> Bookings</th>
                                            <th class="text-end"><i class="bx bx-rupee"></i> Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dashboard['stall_performance'] as $stall)
                                        <tr>
                                            <td>{{ $stall->location_name ?? 'N/A' }}</td>
                                            <td>{{ $stall->table_name ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $stall->total_conversations ?? 0 }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $stall->bookings_count ?? 0 }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-success">
                                                ₹{{ number_format($stall->revenue ?? 0, 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Conversation Timeline -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="bx bx-time-five me-2 text-primary"></i>Conversation Timeline
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(isset($dashboard['conversation_timeline']) && count($dashboard['conversation_timeline']) > 0)
                                <div class="conversation-timeline">
                                    @foreach($dashboard['conversation_timeline'] as $conversation)
                                    <div class="timeline-item {{ $conversation->outcome }}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1 fw-bold">
                                                    @if($conversation->visitor)
                                                        <i class="bx bx-user me-1"></i>{{ $conversation->visitor->name }}
                                                    @else
                                                        <i class="bx bx-phone me-1"></i>{{ $conversation->visitor_phone }}
                                                    @endif
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="bx bx-calendar me-1"></i>{{ $conversation->conversation_date->format('M d, Y h:i A') }}
                                                    @if($conversation->employee)
                                                        | <i class="bx bx-user-circle me-1"></i>{{ $conversation->employee->name }}
                                                    @endif
                                                </small>
                                            </div>
                                            <span class="badge 
                                                @if($conversation->outcome == 'materialised') bg-success
                                                @elseif($conversation->outcome == 'busy') bg-warning
                                                @else bg-info @endif">
                                                {{ ucfirst($conversation->outcome) }}
                                            </span>
                                        </div>
                                        
                                        @if($conversation->location || $conversation->table)
                                        <div class="mb-2">
                                            @if($conversation->location)
                                                <span class="badge bg-secondary me-1">
                                                    <i class="bx bx-map me-1"></i>{{ $conversation->location->loc_name }}
                                                </span>
                                            @endif
                                            @if($conversation->table)
                                                <span class="badge bg-secondary me-1">
                                                    <i class="bx bx-table me-1"></i>{{ $conversation->table->table_no }}
                                                </span>
                                            @endif
                                        </div>
                                        @endif

                                        @if($conversation->campaign)
                                        <div class="mb-2">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-megaphone me-1"></i>Campaign: {{ $conversation->campaign->name }}
                                            </span>
                                        </div>
                                        @endif
                                        @if($conversation->campaignRecipient)
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="bx bx-user-check me-1"></i>Recipient ID: {{ substr($conversation->campaignRecipient->id, 0, 8) }}...
                                            </small>
                                        </div>
                                        @endif

                                        @if($conversation->notes)
                                        <p class="mb-2 text-dark">{{ $conversation->notes }}</p>
                                        @endif

                                        @if($conversation->booking)
                                        <div class="alert alert-success py-2 px-3 mb-0">
                                            <i class="bx bx-check-circle me-1"></i>
                                            <strong>Booking:</strong> ₹{{ number_format($conversation->booking->amount_paid ?? 0, 2) }} 
                                            ({{ ucfirst($conversation->booking->amount_status ?? 'unpaid') }})
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bx bx-message-dots fs-1 text-muted"></i>
                                    <p class="text-muted mt-3">No conversations yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

