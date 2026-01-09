<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $booking->id }}</title>
    <link href="{{ asset('/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
        }
        .invoice-header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .invoice-body {
            margin: 30px 0;
        }
        .invoice-footer {
            border-top: 2px solid #dee2e6;
            padding-top: 20px;
            margin-top: 30px;
        }
        .amount-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-4">
        <div class="row">
            <div class="col-12">
                <!-- Invoice Header -->
                <div class="invoice-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="text-primary mb-0">INVOICE</h2>
                            <p class="text-muted mb-0">Invoice #: {{ substr($booking->id, 0, 12) }}</p>
                            <p class="text-muted mb-0">Date: {{ $booking->booking_date->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h4 class="mb-2">Paridhan CRM</h4>
                            <p class="text-muted mb-0">Exhibition Management System</p>
                        </div>
                    </div>
                </div>

                <!-- Invoice Body -->
                <div class="invoice-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Bill To:</h5>
                            <p class="mb-1"><strong>{{ $booking->exhibitor->name ?? 'N/A' }}</strong></p>
                            <p class="mb-1 text-muted">{{ $booking->exhibitor->email ?? '' }}</p>
                            <p class="mb-1 text-muted">{{ $booking->exhibitor->phone ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Visitor Details:</h5>
                            <p class="mb-1"><strong>{{ $booking->visitor->name ?? 'N/A' }}</strong></p>
                            <p class="mb-1 text-muted">Phone: {{ $booking->visitor->phone ?? $booking->phone ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2">Booking Details:</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Location</th>
                                    <td>{{ $booking->location->loc_name ?? $booking->booking_location ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Table/Stall</th>
                                    <td>{{ $booking->table->table_no ?? $booking->table_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Booking Date</th>
                                    <td>{{ $booking->booking_date->format('F d, Y') }}</td>
                                </tr>
                                @if($booking->campaign)
                                <tr>
                                    <th>Source Campaign</th>
                                    <td>{{ $booking->campaign->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Handled By</th>
                                    <td>{{ $booking->employee->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="amount-box">
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <th>Total Price:</th>
                                        <td class="text-end">₹{{ number_format($booking->price ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount Paid:</th>
                                        <td class="text-end text-success">₹{{ number_format($booking->amount_paid ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount Status:</th>
                                        <td class="text-end">
                                            @php
                                                $statusClass = match($booking->amount_status) {
                                                    'paid' => 'text-success',
                                                    'partial' => 'text-warning',
                                                    'unpaid' => 'text-danger',
                                                    default => 'text-muted'
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }} fw-bold">{{ strtoupper($booking->amount_status ?? 'N/A') }}</span>
                                        </td>
                                    </tr>
                                    @if($booking->amount_status != 'paid')
                                    <tr class="table-warning">
                                        <th>Balance Due:</th>
                                        <td class="text-end fw-bold">₹{{ number_format(($booking->price ?? 0) - ($booking->amount_paid ?? 0), 2) }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Footer -->
                <div class="invoice-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <p class="text-muted mb-0">Thank you for your business!</p>
                            <p class="text-muted small">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Print Button -->
                <div class="no-print text-center mt-4">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="bx bx-printer me-1"></i>Print Invoice
                    </button>
                    <a href="javascript:window.close()" class="btn btn-secondary">
                        <i class="bx bx-x me-1"></i>Close
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
