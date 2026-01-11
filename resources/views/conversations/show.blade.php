@extends('layouts.app_layout')

@section('title', 'Conversation Details')

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Conversation Details</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('conversations.index') }}">Conversations</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <!-- Conversation Details Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bx bx-conversation me-2"></i>Conversation Information
                                </h5>
                                <div>
                                    @if($conversation->booking)
                                        <a href="{{ route('conversations.invoice', $conversation->id) }}" class="btn btn-sm btn-success" target="_blank">
                                            <i class="bx bx-file me-1"></i>View Invoice
                                        </a>
                                    @endif
                                    <a href="{{ route('conversations.index') }}" class="btn btn-sm btn-secondary">
                                        <i class="bx bx-arrow-back me-1"></i>Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Date:</th>
                                            <td>{{ $conversation->conversation_date->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Exhibitor:</th>
                                            <td>{{ $conversation->exhibitor->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Visitor:</th>
                                            <td>{{ $conversation->visitor->name ?? ($conversation->visitor_phone ?? 'N/A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Visitor Phone:</th>
                                            <td>{{ $conversation->visitor_phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Employee:</th>
                                            <td>{{ $conversation->employee->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Location:</th>
                                            <td>{{ $conversation->location->loc_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Table:</th>
                                            <td>{{ $conversation->table->table_no ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Campaign:</th>
                                            <td>{{ $conversation->campaign->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Outcome:</th>
                                            <td>
                                                <span class="badge bg-{{ $conversation->outcome === 'materialised' ? 'success' : ($conversation->outcome === 'interested' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($conversation->outcome) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Notes:</th>
                                            <td>{{ $conversation->notes ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details Card (Admin Only) -->
                    @if($conversation->booking && auth()->user()->role === 'admin')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bx bx-calendar-check me-2"></i>Booking Details
                                </h5>
                                <div>
                                    <a href="{{ route('bookings.invoice', $conversation->booking->id) }}" class="btn btn-sm btn-success me-2" target="_blank">
                                        <i class="bx bx-file me-1"></i>Invoice
                                    </a>
                                    @php
                                        $balance = ($conversation->booking->price ?? 0) - ($conversation->booking->amount_paid ?? 0);
                                    @endphp
                                    @if($balance > 0)
                                        <button onclick="openSettleModal('{{ $conversation->booking->id }}', '{{ $conversation->booking->price }}', '{{ $conversation->booking->amount_paid }}', '{{ $balance }}')" class="btn btn-sm btn-warning me-2">
                                            <i class="bx bx-wallet me-1"></i>Settle
                                        </button>
                                    @endif
                                    <button onclick="releaseTable('{{ $conversation->booking->id }}')" class="btn btn-sm btn-danger">
                                        <i class="bx bx-x me-1"></i>Release Table
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Booking Date:</th>
                                            <td>{{ $conversation->booking->booking_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Price:</th>
                                            <td><strong>₹{{ number_format($conversation->booking->price ?? 0, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Amount Paid:</th>
                                            <td class="text-success"><strong>₹{{ number_format($conversation->booking->amount_paid ?? 0, 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Balance:</th>
                                            <td class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                                <strong>₹{{ number_format($balance, 2) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $conversation->booking->amount_status === 'paid' ? 'success' : ($conversation->booking->amount_status === 'partial' ? 'warning' : 'danger') }}">
                                                    {{ strtoupper($conversation->booking->amount_status ?? 'pending') }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Settle Amount Modal (include if needed) -->
@endsection

@section('script')
    <script>
        function openSettleModal(bookingId, totalPrice, amountPaid, remainingBalance) {
            // Redirect to bookings page with settle modal trigger
            window.location.href = '{{ route("admin.bookings.index") }}?settle=' + bookingId;
        }
        
        function releaseTable(bookingId) {
            if (!confirm('Are you sure you want to release this table? This action cannot be undone.')) {
                return;
            }
            
            $.ajax({
                url: '{{ route("admin.bookings.release", ":id") }}'.replace(':id', bookingId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Table released successfully!');
                        } else {
                            alert(response.message || 'Table released successfully!');
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert(response.message || 'Failed to release table.');
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                    alert(errorMessage);
                }
            });
        }
    </script>
@endsection
