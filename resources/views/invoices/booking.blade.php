<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ substr($booking->id, 0, 12) }} - Paridhan CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            color: #1a1a1a;
            font-size: 14px;
            line-height: 1.6;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 20px auto;
            background: #ffffff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 50px;
            position: relative;
            overflow: hidden;
        }

        .invoice-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .company-info {
            position: relative;
            z-index: 1;
        }

        .company-name {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .company-tagline {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 400;
        }

        .invoice-meta {
            position: relative;
            z-index: 1;
            text-align: right;
        }

        .invoice-label {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 2px;
            opacity: 0.95;
        }

        .invoice-number {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .invoice-date {
            font-size: 14px;
            opacity: 0.9;
        }

        .invoice-body {
            padding: 50px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e8ecf1;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
        }

        .info-box strong {
            display: block;
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .info-box p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 14px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #ffffff;
        }

        .details-table th {
            background: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #495057;
            border-bottom: 2px solid #e8ecf1;
        }

        .details-table td {
            padding: 15px;
            border-bottom: 1px solid #e8ecf1;
            color: #1a1a1a;
        }

        .details-table tr:last-child td {
            border-bottom: none;
        }

        .amount-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 2px solid #e8ecf1;
            border-radius: 12px;
            padding: 30px;
            margin-top: 30px;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e8ecf1;
        }

        .amount-row:last-child {
            border-bottom: none;
        }

        .amount-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .amount-value {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .amount-row.total-row {
            margin-top: 15px;
            padding-top: 20px;
            border-top: 2px solid #667eea;
        }

        .amount-row.total-row .amount-label {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .amount-row.total-row .amount-value {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-partial {
            background: #fff3cd;
            color: #856404;
        }

        .status-pending {
            background: #f8d7da;
            color: #721c24;
        }

        .payment-history {
            margin-top: 40px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .payment-table thead {
            background: #667eea;
            color: white;
        }

        .payment-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payment-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e8ecf1;
            color: #1a1a1a;
        }

        .payment-table tbody tr:hover {
            background: #f8f9fa;
        }

        .invoice-footer {
            background: #f8f9fa;
            padding: 30px 50px;
            border-top: 1px solid #e8ecf1;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 20px;
        }

        .footer-section h6 {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #667eea;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .footer-section p {
            font-size: 13px;
            color: #6c757d;
            margin: 5px 0;
        }

        .footer-note {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e8ecf1;
            color: #6c757d;
            font-size: 13px;
        }

        .action-buttons {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-top: 1px solid #e8ecf1;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        @media print {
            body {
                background: #fff;
            }
            .invoice-container {
                box-shadow: none;
                margin: 0;
            }
            .action-buttons {
                display: none;
            }
            .no-print {
                display: none;
            }
            @page {
                margin: 0;
            }
        }

        @media (max-width: 768px) {
            .invoice-body {
                padding: 30px 20px;
            }
            .invoice-header {
                padding: 30px 20px;
            }
            .info-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .footer-content {
                grid-template-columns: 1fr;
            }
            .invoice-label {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <!-- Header -->
    <div class="invoice-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div class="company-info">
                <div class="company-name">Paridhan CRM</div>
                <div class="company-tagline">Exhibition Management System</div>
            </div>
            <div class="invoice-meta">
                <div class="invoice-label">INVOICE</div>
                <div class="invoice-number">#{{ substr($booking->id, 0, 12) }}</div>
                <div class="invoice-date">{{ $booking->booking_date->format('F d, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Body -->
    <div class="invoice-body">
        <!-- Bill To & Visitor Info -->
        <div class="info-grid">
            <div class="info-box">
                <strong>Bill To</strong>
                <p style="margin-top: 8px; font-weight: 600; color: #1a1a1a;">{{ $booking->exhibitor->name ?? 'N/A' }}</p>
                @if($booking->exhibitor)
                    @if($booking->exhibitor->email)
                        <p><i class="bx bx-envelope"></i> {{ $booking->exhibitor->email }}</p>
                    @endif
                    @if($booking->exhibitor->phone)
                        <p><i class="bx bx-phone"></i> {{ $booking->exhibitor->phone }}</p>
                    @endif
                    @if($booking->exhibitor->address)
                        <p><i class="bx bx-map"></i> {{ $booking->exhibitor->address }}</p>
                    @endif
                @endif
            </div>

            <div class="info-box">
                <strong>Visitor Details</strong>
                <p style="margin-top: 8px; font-weight: 600; color: #1a1a1a;">{{ $booking->visitor->name ?? 'N/A' }}</p>
                @if($booking->visitor && $booking->visitor->phone)
                    <p><i class="bx bx-phone"></i> {{ $booking->visitor->phone }}</p>
                @elseif($booking->phone)
                    <p><i class="bx bx-phone"></i> {{ $booking->phone }}</p>
                @endif
                @if($booking->visitor && $booking->visitor->email)
                    <p><i class="bx bx-envelope"></i> {{ $booking->visitor->email }}</p>
                @endif
            </div>
        </div>

        <!-- Booking Details -->
        <div class="section">
            <div class="section-title">Booking Details</div>
            <table class="details-table">
                @if($booking->campaign)
                <tr style="background: #f0f4ff;">
                    <th style="width: 200px; color: #667eea;">Campaign</th>
                    <td style="font-weight: 600; color: #667eea;">{{ $booking->campaign->name }}</td>
                </tr>
                @endif
                <tr>
                    <th style="width: 200px;">Location</th>
                    <td>{{ $booking->location->loc_name ?? $booking->booking_location ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Table / Stall Number</th>
                    <td>{{ $booking->table->table_no ?? $booking->table_no ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Booking Date</th>
                    <td>{{ $booking->booking_date->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <th>Handled By</th>
                    <td>{{ $booking->employee->name ?? 'N/A' }}</td>
                </tr>
                @if($booking->released_at)
                <tr>
                    <th>Table Released On</th>
                    <td>{{ $booking->released_at->format('F d, Y, h:i A') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Amount Summary -->
        <div class="amount-section">
            <div class="amount-row">
                <span class="amount-label">Subtotal</span>
                <span class="amount-value">₹{{ number_format($booking->price ?? 0, 2) }}</span>
            </div>
            <div class="amount-row">
                <span class="amount-label">Amount Paid</span>
                <span class="amount-value" style="color: #28a745;">₹{{ number_format($booking->amount_paid ?? 0, 2) }}</span>
            </div>
            <div class="amount-row">
                <span class="amount-label">Payment Status</span>
                <span>
                    <span class="status-badge 
                        {{ $booking->amount_status == 'paid' ? 'status-paid' : 
                           ($booking->amount_status == 'partial' ? 'status-partial' : 'status-pending') }}">
                        {{ strtoupper($booking->amount_status ?? 'PENDING') }}
                    </span>
                </span>
            </div>
            @if($booking->amount_status != 'paid')
            <div class="amount-row total-row">
                <span class="amount-label">Balance Due</span>
                <span class="amount-value" style="color: #dc3545;">₹{{ number_format(($booking->price ?? 0) - ($booking->amount_paid ?? 0), 2) }}</span>
            </div>
            @else
            <div class="amount-row total-row">
                <span class="amount-label">Total Paid</span>
                <span class="amount-value" style="color: #28a745;">₹{{ number_format($booking->amount_paid ?? 0, 2) }}</span>
            </div>
            @endif
        </div>

        <!-- Payment History -->
        @if($booking->paymentHistory && $booking->paymentHistory->count() > 0)
        <div class="section payment-history">
            <div class="section-title">Payment History</div>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking->paymentHistory->sortByDesc('payment_date') as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('M d, Y h:i A') }}</td>
                        <td style="font-weight: 600; color: #28a745;">₹{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst($payment->payment_method ?? 'Cash') }}</td>
                        <td>{{ $payment->recorder->name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="invoice-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h6>Payment Terms</h6>
                <p>Payment is due within 30 days of invoice date.</p>
                <p>For any queries, please contact our support team.</p>
            </div>
            <div class="footer-section">
                <h6>Contact Information</h6>
                <p><strong>Email:</strong> support@paridhancrm.com</p>
                <p><strong>Phone:</strong> +91-XXXX-XXXXXX</p>
            </div>
        </div>
        <div class="footer-note">
            <p>This is a computer-generated invoice. No signature required.</p>
            <p style="margin-top: 10px;">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bx bx-printer"></i> Print Invoice
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="bx bx-x"></i> Close
        </button>
    </div>
</div>

</body>
</html>
