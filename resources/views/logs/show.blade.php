@extends('layouts.app_layout')

@section('title', 'View Log: ' . $logFile)

@section('style')
<style>
    .log-content {
        background: #1e1e1e;
        color: #d4d4d4;
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        line-height: 1.6;
        padding: 20px;
        border-radius: 8px;
        max-height: 70vh;
        overflow-y: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .log-line {
        padding: 2px 0;
        border-bottom: 1px solid #2d2d2d;
    }
    
    .log-line:last-child {
        border-bottom: none;
    }
    
    .log-line.error {
        color: #f48771;
    }
    
    .log-line.warning {
        color: #cca700;
    }
    
    .log-line.info {
        color: #4ec9b0;
    }
    
    .log-line.debug {
        color: #9cdcfe;
    }
    
    .log-line.critical {
        color: #f48771;
        font-weight: bold;
    }
    
    /* Custom scrollbar for log content */
    .log-content::-webkit-scrollbar {
        width: 10px;
    }
    
    .log-content::-webkit-scrollbar-track {
        background: #2d2d2d;
        border-radius: 5px;
    }
    
    .log-content::-webkit-scrollbar-thumb {
        background: #555;
        border-radius: 5px;
    }
    
    .log-content::-webkit-scrollbar-thumb:hover {
        background: #777;
    }
    
    .log-info-bar {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">System Logs</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role.'.logs.index') }}">Logs</a></li>
                            <li class="breadcrumb-item active">{{ $logFile }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <!-- Log Info Bar -->
                    <div class="log-info-bar">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">
                                    <i class="bx bx-file-blank me-2 text-primary"></i>{{ $logFile }}
                                </h5>
                                <small class="text-muted">File Size: {{ number_format($fileSize / 1024, 2) }} KB</small>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route(Auth::user()->role.'.logs.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="bx bx-arrow-back me-1"></i>Back to Logs
                                </a>
                                <a href="{{ route(Auth::user()->role.'.logs.download', $logFile) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-download me-1"></i>Download
                                </a>
                                <button type="button" class="btn btn-sm btn-info" onclick="location.reload()">
                                    <i class="bx bx-refresh me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Log Content -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="bx bx-code-alt me-2" style="color: var(--sidebar-end, #3b82f6);"></i>Log Content
                                <small class="text-muted">(Showing last 1000 lines, newest first)</small>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="log-content">
@foreach($lines as $line)
@php
    $lineClass = '';
    if (stripos($line, '.ERROR') !== false || stripos($line, '.CRITICAL') !== false) {
        $lineClass = 'error';
    } elseif (stripos($line, '.WARNING') !== false) {
        $lineClass = 'warning';
    } elseif (stripos($line, '.INFO') !== false) {
        $lineClass = 'info';
    } elseif (stripos($line, '.DEBUG') !== false) {
        $lineClass = 'debug';
    }
@endphp
<span class="log-line {{ $lineClass }}">{{ $line }}</span>
@endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Other Log Files -->
                    @if(count($logFiles) > 1)
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="bx bx-file me-2" style="color: var(--sidebar-end, #3b82f6);"></i>Other Log Files
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @foreach($logFiles as $file)
                                    @if($file !== $logFile)
                                    <a href="{{ route(Auth::user()->role.'.logs.show', $file) }}" class="list-group-item list-group-item-action">
                                        <i class="bx bx-file me-2"></i>{{ $file }}
                                    </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


