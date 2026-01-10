@extends('layouts.app_layout')

@section('title', 'Invoices')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bx bx-file me-2"></i>Invoices & Bookings
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Invoices</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stat-icon" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: #4a6fa5; color: white; border-radius: 8px;">
                                        <i class="bx bx-file fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1" style="font-size: 0.8rem;">Total Invoices</h6>
                                    <h4 class="mb-0" id="totalInvoices">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stat-icon" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: #28a745; color: white; border-radius: 8px;">
                                        <i class="bx bx-rupee fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1" style="font-size: 0.8rem;">Total Revenue</h6>
                                    <h4 class="mb-0" id="totalRevenue">₹0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stat-icon" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: #ffc107; color: white; border-radius: 8px;">
                                        <i class="bx bx-wallet fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1" style="font-size: 0.8rem;">Paid Amount</h6>
                                    <h4 class="mb-0" id="paidAmount">₹0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stat-icon" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: #dc3545; color: white; border-radius: 8px;">
                                        <i class="bx bx-time-five fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1" style="font-size: 0.8rem;">Pending Balance</h6>
                                    <h4 class="mb-0" id="pendingBalance">₹0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bx bx-list-ul me-2"></i>All Invoices
                                </h5>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="invoicesTable.ajax.reload()">
                                        <i class="bx bx-refresh me-1"></i>Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="invoicesTable">
                                    <thead class="table-header-gradient">
                                        <tr>
                                            <th class="text-center" style="width: 80px;">
                                                <i class="bx bx-hash"></i> Invoice #
                                            </th>
                                            <th>
                                                <i class="bx bx-calendar"></i> Date
                                            </th>
                                            <th>
                                                <i class="bx bx-store-alt"></i> Exhibitor
                                            </th>
                                            <th>
                                                <i class="bx bx-user"></i> Visitor
                                            </th>
                                            <th>
                                                <i class="bx bx-map"></i> Location
                                            </th>
                                            <th class="text-center">
                                                <i class="bx bx-table"></i> Table
                                            </th>
                                            <th class="text-end">
                                                <i class="bx bx-rupee"></i> Total Amount
                                            </th>
                                            <th class="text-end">
                                                <i class="bx bx-wallet"></i> Paid
                                            </th>
                                            <th class="text-end">
                                                <i class="bx bx-time"></i> Balance
                                            </th>
                                            <th class="text-center">
                                                <i class="bx bx-check-circle"></i> Status
                                            </th>
                                            <th class="text-center">
                                                <i class="bx bx-cog"></i> Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <link rel="stylesheet" href="{{ asset('assets/css/enhanced-tables.css') }}">
    <script>
        $(document).ready(function() {
            var invoicesTable = $('#invoicesTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('invoices.list') }}",
                    type: "POST",
                    dataSrc: 'data',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'invoice_no', name: 'invoice_no' },
                    { data: 'booking_date', name: 'booking_date' },
                    { data: 'exhibitor', name: 'exhibitor' },
                    { data: 'visitor', name: 'visitor' },
                    { data: 'location', name: 'location' },
                    { data: 'table', name: 'table', className: 'text-center' },
                    { 
                        data: 'price', 
                        name: 'price', 
                        className: 'text-end',
                        render: function(data) {
                            return '₹' + data;
                        }
                    },
                    { 
                        data: 'amount_paid', 
                        name: 'amount_paid', 
                        className: 'text-end',
                        render: function(data) {
                            return '₹' + data;
                        }
                    },
                    { 
                        data: 'balance', 
                        name: 'balance', 
                        className: 'text-end',
                        render: function(data) {
                            const balance = parseFloat(data);
                            if (balance > 0) {
                                return '<span class="text-danger fw-bold">₹' + data + '</span>';
                            } else {
                                return '<span class="text-success">₹0.00</span>';
                            }
                        }
                    },
                    { 
                        data: 'amount_status', 
                        name: 'amount_status', 
                        className: 'text-center',
                        render: function(data) {
                            let badgeClass = 'badge-soft';
                            let icon = 'bx-time';
                            if (data === 'paid') {
                                badgeClass = 'badge bg-success';
                                icon = 'bx-check-circle';
                            } else if (data === 'partial') {
                                badgeClass = 'badge bg-warning';
                                icon = 'bx-time-five';
                            } else {
                                badgeClass = 'badge bg-danger';
                                icon = 'bx-x-circle';
                            }
                            return '<span class="badge ' + badgeClass + '"><i class="bx ' + icon + ' me-1"></i>' + data.toUpperCase() + '</span>';
                        }
                    },
                    { 
                        data: 'id', 
                        name: 'actions', 
                        className: 'text-center',
                        orderable: false,
                        render: function(data) {
                            return `
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ url('admin/bookings') }}/${data}/invoice" 
                                       class="btn btn-sm btn-action btn-primary" 
                                       title="Generate Invoice" 
                                       target="_blank">
                                        <i class="bx bx-file"></i>
                                    </a>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[1, 'desc']],
                pageLength: 25,
                language: {
                    emptyTable: "No invoices found",
                    zeroRecords: "No matching invoices found"
                },
                dom: "<'row'<'col-12 col-md-4'B><'col-12 col-md-4'l><'col-12 col-md-4'f>>" +
                     "<'row'<'col-12'tr>>" +
                     "<'row'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
                drawCallback: function(settings) {
                    // Calculate summary stats
                    var api = this.api();
                    var data = api.rows({page: 'current'}).data();
                    
                    var totalInvoices = api.page.info().recordsTotal;
                    var totalRevenue = 0;
                    var paidAmount = 0;
                    var pendingBalance = 0;
                    
                    api.rows().data().each(function(row) {
                        totalRevenue += parseFloat(row.price.replace(/,/g, ''));
                        paidAmount += parseFloat(row.amount_paid.replace(/,/g, ''));
                        var balance = parseFloat(row.balance.replace(/,/g, ''));
                        if (balance > 0) {
                            pendingBalance += balance;
                        }
                    });
                    
                    $('#totalInvoices').text(totalInvoices);
                    $('#totalRevenue').text('₹' + totalRevenue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#paidAmount').text('₹' + paidAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#pendingBalance').text('₹' + pendingBalance.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                }
            });

            // Make table accessible globally
            window.invoicesTable = invoicesTable;
        });
    </script>
@endsection



