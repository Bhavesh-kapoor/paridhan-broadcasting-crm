@extends('layouts.app_layout')

@section('title', 'Employee Dashboard')

@section('style')
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
        
        .stat-card {
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            border-left-color: var(--sidebar-end);
            box-shadow: 0 4px 16px rgba(30, 58, 138, 0.12);
            transform: translateY(-2px);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%);
            border: none;
            color: white;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.2);
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, var(--sidebar-end) 0%, var(--sidebar-start) 100%);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.4);
            color: white;
        }
    </style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Welcome Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%); border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="text-white mb-1">Welcome Back, {{ auth()->user()->name }}!</h4>
                                    <p class="text-white-50 mb-0">
                                        <i class="bx bx-calendar me-1"></i>
                                        <span id="currentDateTime">{{ now()->format('M d, Y • h:i A') }}</span>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('table-availability.index') }}" class="btn btn-light btn-sm me-2">
                                        <i class="bx bx-table me-1"></i>Table Availability
                                    </a>
                                    <a href="{{ route('employee.campaigns.index') }}" class="btn btn-light btn-sm">
                                        <i class="bx bx-megaphone me-1"></i>All Campaigns
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Primary Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3">
                                    <i class="bx bx-conversation"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">My Conversations</h6>
                                    <h3 class="mb-0">{{ $totalConversations }}</h3>
                                    <small class="text-success">
                                        <i class="bx bx-up-arrow-alt"></i> {{ $todayConversations }} today
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3">
                                    <i class="bx bx-calendar-check"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">My Bookings</h6>
                                    <h3 class="mb-0">{{ $totalBookings }}</h3>
                                    <small class="text-success">
                                        <i class="bx bx-up-arrow-alt"></i> {{ $todayBookings }} today
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card" style="border-left: 4px solid #22c55e;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                                    <i class="bx bx-rupee"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">Total Revenue</h6>
                                    <h3 class="mb-0 text-success">₹{{ number_format($totalRevenue, 2) }}</h3>
                                    <small class="text-muted">
                                        <i class="bx bx-calendar"></i> ₹{{ number_format($todayRevenue, 2) }} today
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card" style="border-left: 4px solid #3b82f6;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                    <i class="bx bx-trophy"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">Conversion Rate</h6>
                                    <h3 class="mb-0">{{ $conversionRate }}%</h3>
                                    <small class="text-muted">
                                        {{ $materialisedConversations }} materialised
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-left: 3px solid #22c55e;">
                        <div class="card-body p-3 text-center">
                            <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; margin: 0 auto 8px;">
                                <i class="bx bx-check-circle"></i>
                            </div>
                            <h5 class="mb-0 text-success">{{ $materialisedConversations }}</h5>
                            <small class="text-muted">Materialised</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #dbeafe 0%, #ffffff 100%); border-left: 3px solid #3b82f6;">
                        <div class="card-body p-3 text-center">
                            <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; margin: 0 auto 8px;">
                                <i class="bx bx-star"></i>
                            </div>
                            <h5 class="mb-0" style="color: #3b82f6;">{{ $interestedConversations }}</h5>
                            <small class="text-muted">Interested</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fef3c7 0%, #ffffff 100%); border-left: 3px solid #f59e0b;">
                        <div class="card-body p-3 text-center">
                            <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; margin: 0 auto 8px;">
                                <i class="bx bx-time"></i>
                            </div>
                            <h5 class="mb-0" style="color: #f59e0b;">{{ $busyConversations }}</h5>
                            <small class="text-muted">Busy</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-left: 3px solid #64748b;">
                        <div class="card-body p-3 text-center">
                            <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #64748b 0%, #475569 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; margin: 0 auto 8px;">
                                <i class="bx bx-megaphone"></i>
                            </div>
                            <h5 class="mb-0">{{ $totalActiveCampaigns }}</h5>
                            <small class="text-muted">Active Campaigns</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%); border-left: 3px solid #10b981;">
                        <div class="card-body p-3 text-center">
                            <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; margin: 0 auto 8px;">
                                <i class="bx bx-calendar-week"></i>
                            </div>
                            <h5 class="mb-0 text-success">₹{{ number_format($thisWeekRevenue, 0) }}</h5>
                            <small class="text-muted">This Week</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-left: 3px solid #2563eb;">
                        <div class="card-body p-3 text-center">
                            <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; margin: 0 auto 8px;">
                                <i class="bx bx-calendar"></i>
                            </div>
                            <h5 class="mb-0" style="color: #2563eb;">₹{{ number_format($thisMonthRevenue, 0) }}</h5>
                            <small class="text-muted">This Month</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown Section -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-left: 4px solid #22c55e;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">Paid Revenue</h6>
                                    <h4 class="mb-0 text-success">₹{{ number_format($paidRevenue, 2) }}</h4>
                                    <small class="text-muted">
                                        {{ $totalPrice > 0 ? round(($paidRevenue / $totalPrice) * 100, 1) : 0 }}% of total
                                    </small>
                                </div>
                                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);">
                                    <i class="bx bx-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fef3c7 0%, #ffffff 100%); border-left: 4px solid #f59e0b;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">Pending Revenue</h6>
                                    <h4 class="mb-0" style="color: #f59e0b;">₹{{ number_format($pendingRevenue, 2) }}</h4>
                                    <small class="text-muted">
                                        {{ $totalPrice > 0 ? round(($pendingRevenue / $totalPrice) * 100, 1) : 0 }}% of total
                                    </small>
                                </div>
                                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                                    <i class="bx bx-time"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fce7f3 0%, #ffffff 100%); border-left: 4px solid #ec4899;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">Partial Paid</h6>
                                    <h4 class="mb-0" style="color: #ec4899;">₹{{ number_format($partialRevenue, 2) }}</h4>
                                    <small class="text-muted">
                                        {{ $totalPrice > 0 ? round(($partialRevenue / $totalPrice) * 100, 1) : 0 }}% of total
                                    </small>
                                </div>
                                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);">
                                    <i class="bx bx-wallet-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Campaigns -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bx bx-megaphone me-2" style="color: var(--sidebar-end);"></i>Active Campaigns
                                </h5>
                                <a href="{{ route('employee.campaigns.index') }}" class="btn btn-sm btn-gradient">
                                    <i class="bx bx-list-ul me-1"></i>View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($activeCampaigns->count() > 0)
                                <div class="row g-3">
                                    @foreach($activeCampaigns as $campaign)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card campaign-card" onclick="window.location.href='{{ route('employee.campaigns.recipients', $campaign->id) }}'">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0 fw-semibold">{{ $campaign->name }}</h6>
                                                    <span class="badge bg-{{ $campaign->status == 'sent' ? 'success' : 'info' }}">
                                                        {{ strtoupper($campaign->status) }}
                                                    </span>
                                                </div>
                                                <p class="text-muted small mb-2">{{ Str::limit($campaign->subject, 50) }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bx bx-calendar me-1"></i>{{ $campaign->created_at->format('M d, Y') }}
                                                    </small>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); window.location.href='{{ route('employee.campaigns.recipients', $campaign->id) }}'">
                                                        <i class="bx bx-user me-1"></i>View Visitors
                                                    </button>
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

            <!-- Recent Conversations & Bookings -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0;">
                            <h6 class="mb-0 fw-bold">
                                <i class="bx bx-conversation me-2" style="color: var(--sidebar-end);"></i>Recent Conversations
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($myConversations->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($myConversations as $conversation)
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $conversation->exhibitor->name ?? 'N/A' }}</h6>
                                                <p class="text-muted small mb-1">
                                                    <i class="bx bx-user me-1"></i>{{ $conversation->visitor->name ?? $conversation->visitor_phone ?? 'N/A' }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bx bx-calendar me-1"></i>{{ $conversation->conversation_date->format('M d, Y H:i') }}
                                                </small>
                                            </div>
                                            <span class="badge bg-{{ $conversation->outcome == 'materialised' ? 'success' : ($conversation->outcome == 'interested' ? 'info' : 'warning') }}">
                                                {{ ucfirst($conversation->outcome) }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bx bx-conversation fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No conversations yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-calendar-check me-2" style="color: var(--sidebar-end);"></i>My Bookings & Revenue
                                </h6>
                                <a href="{{ route('employee.bookings.index') }}" class="btn btn-sm btn-gradient">
                                    <i class="bx bx-list-ul me-1"></i>View All Bookings
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($myBookings->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Exhibitor</th>
                                                <th>Booking Date</th>
                                                <th class="text-end">Total</th>
                                                <th class="text-end">Paid</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($myBookings as $booking)
                                            <tr>
                                                <td>
                                                    <strong>{{ $booking->exhibitor->name ?? 'N/A' }}</strong>
                                                    @if($booking->visitor)
                                                    <br><small class="text-muted">{{ $booking->visitor->name }}</small>
                                                    @endif
                                                    @if($booking->location)
                                                    <br><small class="text-muted"><i class="bx bx-map"></i> {{ $booking->location->loc_name }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $booking->booking_date->format('M d, Y') }}</td>
                                                <td class="text-end">
                                                    <strong>₹{{ number_format($booking->price ?? 0, 2) }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-success">₹{{ number_format($booking->amount_paid ?? 0, 2) }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $statusClass = match($booking->amount_status) {
                                                            'paid' => 'success',
                                                            'partial' => 'warning',
                                                            'pending' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}">
                                                        {{ ucfirst($booking->amount_status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-active">
                                                <th colspan="2">Total</th>
                                                <th class="text-end">₹{{ number_format($myBookings->sum('price'), 2) }}</th>
                                                <th class="text-end text-success">₹{{ number_format($myBookings->sum('amount_paid'), 2) }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="mt-3 pt-3 border-top">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block">Total Bookings</small>
                                            <strong>{{ $totalBookings }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Total Revenue</small>
                                            <strong class="text-success">₹{{ number_format($totalRevenue, 2) }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Pending</small>
                                            <strong>₹{{ number_format($pendingRevenue, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bx bx-calendar-check fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No bookings yet</p>
                                    <small class="text-muted">Start by adding conversations and booking tables</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Update Date and Time
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { month: 'short', day: 'numeric', year: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit' };
            
            const dateStr = now.toLocaleDateString('en-US', dateOptions);
            const timeStr = now.toLocaleTimeString('en-US', timeOptions);
            
            document.getElementById('currentDateTime').textContent = dateStr + ' • ' + timeStr;
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
@endsection

