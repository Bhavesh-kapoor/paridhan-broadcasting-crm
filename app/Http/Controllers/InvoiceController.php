<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\PaymentHistory;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices (bookings)
     */
    public function index(): View
    {
        return view('invoices.index');
    }

    /**
     * Get all bookings for DataTable (AJAX)
     */
    public function getAllInvoices(Request $request): JsonResponse
    {
        $bookings = Booking::with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign'])
            ->orderBy('booking_date', 'desc')
            ->get();

        $data = [];
        foreach ($bookings as $index => $booking) {
            $balance = ($booking->price ?? 0) - ($booking->amount_paid ?? 0);
            
            $data[] = [
                'id' => $booking->id,
                'invoice_no' => substr($booking->id, 0, 12),
                'booking_date' => $booking->booking_date->format('M d, Y'),
                'exhibitor' => $booking->exhibitor->name ?? 'N/A',
                'visitor' => $booking->visitor->name ?? ($booking->phone ?? 'N/A'),
                'location' => $booking->location->loc_name ?? $booking->booking_location ?? 'N/A',
                'table' => $booking->table->table_no ?? $booking->table_no ?? 'N/A',
                'price' => number_format($booking->price ?? 0, 2),
                'amount_paid' => number_format($booking->amount_paid ?? 0, 2),
                'balance' => number_format($balance, 2),
                'amount_status' => $booking->amount_status ?? 'pending',
                'campaign' => $booking->campaign->name ?? 'N/A',
                'employee' => $booking->employee->name ?? 'N/A',
                'created_at' => $booking->created_at->format('M d, Y'),
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Generate invoice for a booking (Admin and Employee access)
     */
    public function generate($bookingId): View
    {
        $booking = Booking::with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'paymentHistory.recorder'])
            ->findOrFail($bookingId);
        
        // Check permissions - employees can only view their own bookings, admin can view all
        if (auth()->user()->role === 'employee' && $booking->employee_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        return view('invoices.booking', compact('booking'));
    }

    /**
     * Generate invoice from a conversation (if it has a booking)
     */
    public function generateFromConversation($conversationId): View
    {
        $conversation = Conversation::with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'booking'])
            ->findOrFail($conversationId);
        
        // Check permissions - employees can only view their own conversations
        if (auth()->user()->role === 'employee' && $conversation->employee_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        if (!$conversation->booking) {
            abort(404, 'No booking found for this conversation');
        }
        
        $booking = $conversation->booking;
        
        return view('invoices.booking', compact('booking', 'conversation'));
    }

    /**
     * Employee bookings listing
     */
    public function employeeBookings(): View
    {
        $campaigns = Campaign::orderBy('name')->get();
        
        return view('bookings.employee_index', compact('campaigns'));
    }

    /**
     * Get employee bookings statistics (for cards)
     */
    public function getEmployeeBookingsStats(Request $request): JsonResponse
    {
        $employeeId = auth()->id();
        
        $query = Booking::where('employee_id', $employeeId);

        // Apply same filters as getEmployeeBookings
        if ($request->has('status') && $request->status && $request->status !== '') {
            $query->where('amount_status', $request->status);
        }

        if ($request->has('campaign_id') && $request->campaign_id && $request->campaign_id !== '') {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->has('min_price') && $request->min_price !== null && $request->min_price !== '') {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price !== null && $request->max_price !== '') {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('min_balance') && $request->min_balance !== null && $request->min_balance !== '') {
            $query->whereRaw('(price - COALESCE(amount_paid, 0)) >= ?', [$request->min_balance]);
        }

        if ($request->has('max_balance') && $request->max_balance !== null && $request->max_balance !== '') {
            $query->whereRaw('(price - COALESCE(amount_paid, 0)) <= ?', [$request->max_balance]);
        }

        if ($request->has('date_from') && $request->date_from && $request->date_from !== '') {
            $query->where('booking_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to && $request->date_to !== '') {
            $query->where('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->get();
        
        $totalBookings = $bookings->count();
        $totalRevenue = $bookings->sum('amount_paid') ?? 0;
        $paidRevenue = $bookings->where('amount_status', 'paid')->sum('amount_paid') ?? 0;
        $pendingRevenue = $bookings->where('amount_status', 'pending')->sum('price') ?? 0;
        $totalPrice = $bookings->sum('price') ?? 0;
        $totalBalance = $totalPrice - $totalRevenue;
        
        $remainingBalance = $bookings->filter(function($b) { 
            return is_null($b->released_at) && (($b->price ?? 0) - ($b->amount_paid ?? 0)) > 0;
        })->sum(function($b) { 
            return ($b->price ?? 0) - ($b->amount_paid ?? 0); 
        });
        $remainingCount = $bookings->filter(function($b) { 
            return is_null($b->released_at) && (($b->price ?? 0) - ($b->amount_paid ?? 0)) > 0;
        })->count();

        return response()->json([
            'total_bookings' => $totalBookings,
            'total_revenue' => number_format($totalRevenue, 2),
            'paid_revenue' => number_format($paidRevenue, 2),
            'pending_revenue' => number_format($pendingRevenue, 2),
            'total_price' => number_format($totalPrice, 2),
            'total_balance' => number_format($totalBalance, 2),
            'remaining_balance' => number_format($remainingBalance, 2),
            'remaining_count' => $remainingCount
        ]);
    }

    /**
     * Get employee bookings for DataTable (AJAX) with filters
     */
    public function getEmployeeBookings(Request $request): JsonResponse
    {
        $employeeId = auth()->id();
        
        $query = Booking::where('employee_id', $employeeId)
            ->with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'conversation']);

        // Apply filters
        if ($request->has('status') && $request->status && $request->status !== '') {
            $query->where('amount_status', $request->status);
        }

        if ($request->has('campaign_id') && $request->campaign_id && $request->campaign_id !== '') {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->has('min_price') && $request->min_price !== null && $request->min_price !== '') {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price !== null && $request->max_price !== '') {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('min_balance') && $request->min_balance !== null && $request->min_balance !== '') {
            $query->whereRaw('(price - COALESCE(amount_paid, 0)) >= ?', [$request->min_balance]);
        }

        if ($request->has('max_balance') && $request->max_balance !== null && $request->max_balance !== '') {
            $query->whereRaw('(price - COALESCE(amount_paid, 0)) <= ?', [$request->max_balance]);
        }

        if ($request->has('date_from') && $request->date_from && $request->date_from !== '') {
            $query->where('booking_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to && $request->date_to !== '') {
            $query->where('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->get();

        $data = [];
        foreach ($bookings as $index => $booking) {
            $balance = ($booking->price ?? 0) - ($booking->amount_paid ?? 0);
            
            $data[] = [
                'id' => $booking->id,
                'invoice_no' => substr($booking->id, 0, 12),
                'booking_date' => $booking->booking_date->format('M d, Y'),
                'exhibitor' => $booking->exhibitor->name ?? 'N/A',
                'visitor' => $booking->visitor->name ?? ($booking->phone ?? 'N/A'),
                'location' => $booking->location->loc_name ?? $booking->booking_location ?? 'N/A',
                'table' => $booking->table->table_no ?? $booking->table_no ?? 'N/A',
                'price' => number_format($booking->price ?? 0, 2),
                'amount_paid' => number_format($booking->amount_paid ?? 0, 2),
                'balance' => number_format($balance, 2),
                'amount_status' => $booking->amount_status ?? 'pending',
                'campaign' => $booking->campaign->name ?? 'N/A',
                'conversation_id' => $booking->conversation->id ?? null,
                'created_at' => $booking->created_at->format('M d, Y'),
                'released_at' => $booking->released_at ? $booking->released_at->toIso8601String() : null,
                'released_at_formatted' => $booking->released_at ? $booking->released_at->format('M d, Y H:i') : null,
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Settle remaining amount for a booking
     */
    public function settleBookingAmount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|ulid|exists:bookings,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:50',
        ]);

        try {
            $booking = Booking::findOrFail($validated['booking_id']);
            
            // Check if employee owns this booking
            if (auth()->user()->role === 'employee' && $booking->employee_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            $totalPrice = (float) $booking->price;
            $currentAmountPaid = (float) $booking->amount_paid;
            $settlementAmount = (float) $validated['amount'];
            $remainingBalance = $totalPrice - $currentAmountPaid;
            
            // Validate that settlement amount doesn't exceed remaining balance
            if ($settlementAmount > $remainingBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Settlement amount cannot exceed the remaining balance of ₹' . number_format($remainingBalance, 2)
                ], 422);
            }
            
            // Calculate new amount paid
            $newAmountPaid = $currentAmountPaid + $settlementAmount;
            
            // Determine new status
            $newStatus = 'partial';
            if ($newAmountPaid >= $totalPrice) {
                $newStatus = 'paid';
                $newAmountPaid = $totalPrice; // Ensure we don't overpay
            } elseif ($newAmountPaid > 0) {
                $newStatus = 'partial';
            } else {
                $newStatus = 'pending';
            }
            
            // Update booking
            $booking->amount_paid = $newAmountPaid;
            $booking->amount_status = $newStatus;
            $booking->save();
            
            // Record payment history
            PaymentHistory::create([
                'booking_id' => $booking->id,
                'amount' => $settlementAmount,
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'recorded_by' => auth()->id(),
                'payment_date' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Amount settled successfully',
                'booking' => [
                    'amount_paid' => number_format($newAmountPaid, 2),
                    'amount_status' => $newStatus,
                    'remaining_balance' => number_format($totalPrice - $newAmountPaid, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error settling amount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin bookings listing
     */
    public function adminBookings(): View
    {
        $campaigns = Campaign::orderBy('name')->get();
        $employees = \App\Models\User::where('role', 'employee')->where('status', 'active')->orderBy('name')->get();
        
        return view('bookings.admin_index', compact('campaigns', 'employees'));
    }

    /**
     * Get admin bookings statistics (for cards)
     */
    public function getAdminBookingsStats(Request $request): JsonResponse
    {
        try {
            // Verify user is admin
            if (auth()->user()->role !== 'admin') {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 403);
            }

            $query = Booking::query();

            // Apply same filters as getAdminBookings
            if ($request->has('status') && $request->status && $request->status !== '') {
                $query->where('amount_status', $request->status);
            }

            if ($request->has('campaign_id') && $request->campaign_id && $request->campaign_id !== '') {
                $query->where('campaign_id', $request->campaign_id);
            }

            if ($request->has('employee_id') && $request->employee_id && $request->employee_id !== '') {
                $query->where('employee_id', $request->employee_id);
            }

            if ($request->has('min_price') && $request->min_price !== null && $request->min_price !== '') {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price') && $request->max_price !== null && $request->max_price !== '') {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->has('min_balance') && $request->min_balance !== null && $request->min_balance !== '') {
                $query->whereRaw('(price - COALESCE(amount_paid, 0)) >= ?', [$request->min_balance]);
            }

            if ($request->has('max_balance') && $request->max_balance !== null && $request->max_balance !== '') {
                $query->whereRaw('(price - COALESCE(amount_paid, 0)) <= ?', [$request->max_balance]);
            }

            if ($request->has('date_from') && $request->date_from && $request->date_from !== '') {
                $query->where('booking_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to && $request->date_to !== '') {
                $query->where('booking_date', '<=', $request->date_to);
            }

            $bookings = $query->get();
            
            $totalBookings = $bookings->count();
            $totalRevenue = $bookings->sum('amount_paid') ?? 0;
            $paidRevenue = $bookings->where('amount_status', 'paid')->sum('amount_paid') ?? 0;
            $pendingRevenue = $bookings->where('amount_status', 'pending')->sum('price') ?? 0;
            $totalPrice = $bookings->sum('price') ?? 0;
            $totalBalance = $totalPrice - $totalRevenue;
            
            $remainingBalance = $bookings->filter(function($b) { 
                return is_null($b->released_at) && (($b->price ?? 0) - ($b->amount_paid ?? 0)) > 0;
            })->sum(function($b) { 
                return ($b->price ?? 0) - ($b->amount_paid ?? 0); 
            });
            $remainingCount = $bookings->filter(function($b) { 
                return is_null($b->released_at) && (($b->price ?? 0) - ($b->amount_paid ?? 0)) > 0;
            })->count();

            return response()->json([
                'total_bookings' => $totalBookings,
                'total_revenue' => number_format($totalRevenue, 2),
                'paid_revenue' => number_format($paidRevenue, 2),
                'pending_revenue' => number_format($pendingRevenue, 2),
                'total_price' => number_format($totalPrice, 2),
                'total_balance' => number_format($totalBalance, 2),
                'remaining_balance' => number_format($remainingBalance, 2),
                'remaining_count' => $remainingCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getAdminBookingsStats: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while loading statistics'
            ], 500);
        }
    }

    /**
     * Get admin bookings for DataTable (AJAX) - shows all bookings with filters
     * Admin can see ALL bookings including released ones
     */
    public function getAdminBookings(Request $request): JsonResponse
    {
        try {
            // Verify user is admin (extra check for security)
            if (auth()->user()->role !== 'admin') {
                return response()->json([
                    'data' => [],
                    'error' => 'Unauthorized access'
                ], 403);
            }

            // Get all bookings - no restrictions for admin (includes released bookings)
            $query = Booking::with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'conversation']);

            // Apply filters only if provided
            if ($request->has('status') && $request->status && $request->status !== '') {
                $query->where('amount_status', $request->status);
            }

            if ($request->has('campaign_id') && $request->campaign_id && $request->campaign_id !== '') {
                $query->where('campaign_id', $request->campaign_id);
            }

            if ($request->has('employee_id') && $request->employee_id && $request->employee_id !== '') {
                $query->where('employee_id', $request->employee_id);
            }

            if ($request->has('date_from') && $request->date_from && $request->date_from !== '') {
                $query->where('booking_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to && $request->date_to !== '') {
                $query->where('booking_date', '<=', $request->date_to);
            }

            if ($request->has('min_price') && $request->min_price !== null && $request->min_price !== '') {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price') && $request->max_price !== null && $request->max_price !== '') {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->has('min_balance') && $request->min_balance !== null && $request->min_balance !== '') {
                $query->whereRaw('(price - COALESCE(amount_paid, 0)) >= ?', [$request->min_balance]);
            }

            if ($request->has('max_balance') && $request->max_balance !== null && $request->max_balance !== '') {
                $query->whereRaw('(price - COALESCE(amount_paid, 0)) <= ?', [$request->max_balance]);
            }

            // Order by booking date descending (newest first)
            $bookings = $query->orderBy('booking_date', 'desc')->orderBy('created_at', 'desc')->get();

            $data = [];
            foreach ($bookings as $index => $booking) {
                $balance = ($booking->price ?? 0) - ($booking->amount_paid ?? 0);
                
                $data[] = [
                    'id' => $booking->id,
                    'invoice_no' => substr($booking->id, 0, 12),
                    'booking_date' => $booking->booking_date ? $booking->booking_date->format('M d, Y') : 'N/A',
                    'exhibitor' => $booking->exhibitor->name ?? 'N/A',
                    'visitor' => $booking->visitor->name ?? ($booking->phone ?? 'N/A'),
                    'employee' => $booking->employee->name ?? 'N/A',
                    'location' => $booking->location->loc_name ?? $booking->booking_location ?? 'N/A',
                    'table' => $booking->table->table_no ?? $booking->table_no ?? 'N/A',
                    'price' => number_format($booking->price ?? 0, 2),
                    'amount_paid' => number_format($booking->amount_paid ?? 0, 2),
                    'balance' => number_format($balance, 2),
                    'amount_status' => $booking->amount_status ?? 'pending',
                    'campaign' => $booking->campaign->name ?? 'N/A',
                    'conversation_id' => $booking->conversation->id ?? null,
                    'created_at' => $booking->created_at ? $booking->created_at->format('M d, Y') : 'N/A',
                    'released_at' => $booking->released_at ? $booking->released_at->toIso8601String() : null,
                    'released_at_formatted' => $booking->released_at ? $booking->released_at->format('M d, Y H:i') : null,
                ];
            }

            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getAdminBookings: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'error' => 'An error occurred while loading bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Settle remaining amount for a booking (Admin)
     */
    public function settleAdminBookingAmount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|ulid|exists:bookings,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            // Get the booking (admin can settle any booking)
            $booking = Booking::findOrFail($validated['booking_id']);
            
            $totalPrice = (float) $booking->price;
            $currentAmountPaid = (float) $booking->amount_paid;
            $settlementAmount = (float) $validated['amount'];
            $remainingBalance = $totalPrice - $currentAmountPaid;
            
            // Validate that settlement amount doesn't exceed remaining balance
            if ($settlementAmount > $remainingBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Settlement amount cannot exceed the remaining balance of ₹' . number_format($remainingBalance, 2)
                ], 422);
            }
            
            // Calculate new amount paid
            $newAmountPaid = $currentAmountPaid + $settlementAmount;
            
            // Determine new status
            $newStatus = 'partial';
            if ($newAmountPaid >= $totalPrice) {
                $newStatus = 'paid';
                $newAmountPaid = $totalPrice; // Ensure we don't overpay
            } elseif ($newAmountPaid > 0) {
                $newStatus = 'partial';
            } else {
                $newStatus = 'pending';
            }
            
            // Update booking
            $booking->amount_paid = $newAmountPaid;
            $booking->amount_status = $newStatus;
            $booking->save();
            
            // Record payment history
            PaymentHistory::create([
                'booking_id' => $booking->id,
                'amount' => $settlementAmount,
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'recorded_by' => auth()->id(),
                'payment_date' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Amount settled successfully',
                'booking' => [
                    'amount_paid' => number_format($newAmountPaid, 2),
                    'amount_status' => $newStatus,
                    'remaining_balance' => number_format($totalPrice - $newAmountPaid, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error settling amount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history for a booking
     */
    public function getPaymentHistory($bookingId): JsonResponse
    {
        $booking = Booking::findOrFail($bookingId);
        
        // Check permissions
        if (auth()->user()->role === 'employee' && $booking->employee_id !== auth()->id()) {
            return response()->json([
                'error' => 'Unauthorized access'
            ], 403);
        }
        
        $payments = PaymentHistory::where('booking_id', $bookingId)
            ->with('recorder')
            ->orderBy('payment_date', 'desc')
            ->get();
        
        $data = $payments->map(function($payment) {
            return [
                'id' => $payment->id,
                'amount' => number_format($payment->amount, 2),
                'payment_method' => $payment->payment_method ?? 'N/A',
                'payment_date' => $payment->payment_date->format('M d, Y H:i'),
                'recorded_by' => $payment->recorder->name ?? 'N/A',
            ];
        });
        
        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Release table for a booking
     */
    public function releaseTable(Request $request, $bookingId): JsonResponse
    {
        $booking = Booking::findOrFail($bookingId);
        
        // Check permissions
        if (auth()->user()->role === 'employee' && $booking->employee_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        if ($booking->released_at) {
            return response()->json([
                'success' => false,
                'message' => 'Table already released'
            ], 422);
        }
        
        $booking->released_at = now();
        $booking->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Table released successfully'
        ]);
    }

    /**
     * Get bookings with remaining balance (for employee)
     */
    public function getRemainingBalanceBookings(Request $request): JsonResponse
    {
        $employeeId = auth()->id();
        
        $query = Booking::where('employee_id', $employeeId)
            ->whereNull('released_at')
            ->with(['exhibitor', 'visitor', 'location', 'campaign']);
        
        // Only bookings with remaining balance
        $query->whereRaw('(price - COALESCE(amount_paid, 0)) > 0');
        
        // Filter by campaign if provided
        if ($request->has('campaign_id') && $request->campaign_id) {
            $query->where('campaign_id', $request->campaign_id);
        }
        
        $bookings = $query->orderBy('booking_date', 'desc')->get();
        
        $data = $bookings->map(function($booking) {
            $balance = ($booking->price ?? 0) - ($booking->amount_paid ?? 0);
            return [
                'id' => $booking->id,
                'booking_date' => $booking->booking_date->format('M d, Y'),
                'exhibitor' => $booking->exhibitor->name ?? 'N/A',
                'visitor' => $booking->visitor->name ?? ($booking->phone ?? 'N/A'),
                'campaign' => $booking->campaign->name ?? 'N/A',
                'location' => $booking->location->loc_name ?? $booking->booking_location ?? 'N/A',
                'price' => number_format($booking->price ?? 0, 2),
                'amount_paid' => number_format($booking->amount_paid ?? 0, 2),
                'balance' => number_format($balance, 2),
                'amount_status' => $booking->amount_status ?? 'pending',
            ];
        });
        
        return response()->json([
            'status' => true,
            'data' => $data,
            'total_balance' => number_format($bookings->sum(function($b) { return ($b->price ?? 0) - ($b->amount_paid ?? 0); }), 2)
        ]);
    }

    /**
     * Get bookings with remaining balance (for admin)
     */
    public function getAdminRemainingBalanceBookings(Request $request): JsonResponse
    {
        $query = Booking::whereNull('released_at')
            ->with(['exhibitor', 'visitor', 'location', 'campaign', 'employee']);
        
        // Only bookings with remaining balance
        $query->whereRaw('(price - COALESCE(amount_paid, 0)) > 0');
        
        // Filter by campaign if provided
        if ($request->has('campaign_id') && $request->campaign_id) {
            $query->where('campaign_id', $request->campaign_id);
        }
        
        // Filter by employee if provided
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        
        $bookings = $query->orderBy('booking_date', 'desc')->get();
        
        $data = $bookings->map(function($booking) {
            $balance = ($booking->price ?? 0) - ($booking->amount_paid ?? 0);
            return [
                'id' => $booking->id,
                'booking_date' => $booking->booking_date->format('M d, Y'),
                'exhibitor' => $booking->exhibitor->name ?? 'N/A',
                'visitor' => $booking->visitor->name ?? ($booking->phone ?? 'N/A'),
                'employee' => $booking->employee->name ?? 'N/A',
                'campaign' => $booking->campaign->name ?? 'N/A',
                'location' => $booking->location->loc_name ?? $booking->booking_location ?? 'N/A',
                'price' => number_format($booking->price ?? 0, 2),
                'amount_paid' => number_format($booking->amount_paid ?? 0, 2),
                'balance' => number_format($balance, 2),
                'amount_status' => $booking->amount_status ?? 'pending',
            ];
        });
        
        return response()->json([
            'status' => true,
            'data' => $data,
            'total_balance' => number_format($bookings->sum(function($b) { return ($b->price ?? 0) - ($b->amount_paid ?? 0); }), 2)
        ]);
    }
}
