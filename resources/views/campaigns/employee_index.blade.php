@extends('layouts.app_layout')

@section('title', 'Campaigns')

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
    </style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Campaigns</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active">Campaigns</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                @if($campaigns->count() > 0)
                    @foreach($campaigns as $campaign)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card campaign-card" onclick="window.location.href='{{ route('employee.campaigns.recipients', $campaign->id) }}'">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="mb-0 fw-semibold">{{ $campaign->name }}</h5>
                                    <span class="badge bg-{{ $campaign->status == 'sent' ? 'success' : 'info' }}">
                                        {{ strtoupper($campaign->status) }}
                                    </span>
                                </div>
                                <p class="text-muted mb-3">{{ Str::limit($campaign->subject, 80) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bx bx-calendar me-1"></i>{{ $campaign->created_at->format('M d, Y') }}
                                    </small>
                                    <button class="btn btn-sm btn-gradient" onclick="event.stopPropagation(); window.location.href='{{ route('employee.campaigns.recipients', $campaign->id) }}'">
                                        <i class="bx bx-user me-1"></i>View Visitors
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="bx bx-megaphone fs-1 text-muted mb-3"></i>
                                <p class="text-muted mb-0">No active campaigns found</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection






