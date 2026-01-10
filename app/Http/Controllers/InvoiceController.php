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
     * List all invoices (bookings)
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
        $employeeId = auth()->id();
        $campaigns = Campaign::whereHas('bookings', function($q) use ($employeeId) {
            $q->where('employee_id', $employeeId);
        })->orderBy('name')->get();
        
        return view('bookings.employee_index', compact('campaigns'));
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
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $employeeId = auth()->id();
            
            // Get the booking and ensure it belongs to the current employee
            $booking = Booking::where('id', $validated['booking_id'])
                ->where('employee_id', $employeeId)
                ->firstOrFail();
            
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
                'amount_before' => $currentAmountPaid,
                'amount_after' => $newAmountPaid,
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => $employeeId,
                'payment_date' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Amount settled successfully! New balance: ₹' . number_format($totalPrice - $newAmountPaid, 2),
                'data' => [
                    'amount_paid' => number_format($newAmountPaid, 2),
                    'balance' => number_format($totalPrice - $newAmountPaid, 2),
                    'amount_status' => $newStatus
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or you do not have permission to settle this booking.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while settling the amount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin bookings listing
     */
    public function adminBookings(): View
    {
        $campaigns = Campaign::whereHas('bookings')->orderBy('name')->get();
        $employees = \App\Models\User::where('role', 'employee')->where('status', 'active')->orderBy('name')->get();
        
        return view('bookings.admin_index', compact('campaigns', 'employees'));
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
                'amount_before' => $currentAmountPaid,
                'amount_after' => $newAmountPaid,
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => auth()->id(),
                'payment_date' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Amount settled successfully! New balance: ₹' . number_format($totalPrice - $newAmountPaid, 2),
                'data' => [
                    'amount_paid' => number_format($newAmountPaid, 2),
                    'balance' => number_format($totalPrice - $newAmountPaid, 2),
                    'amount_status' => $newStatus
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while settling the amount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history for a booking
     */
    public function getPaymentHistory(Request $request, $bookingId): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            // Check if user has permission (employee can only see their own bookings, admin can see all)
            if (auth()->user()->role === 'employee' && $booking->employee_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view this booking.'
                ], 403);
            }
            
            $paymentHistory = PaymentHistory::where('booking_id', $bookingId)
                ->with('recorder')
                ->orderBy('payment_date', 'desc')
                ->get();
            
            $data = [];
            foreach ($paymentHistory as $payment) {
                $data[] = [
                    'id' => $payment->id,
                    'amount' => number_format($payment->amount, 2),
                    'amount_before' => number_format($payment->amount_before ?? 0, 2),
                    'amount_after' => number_format($payment->amount_after, 2),
                    'payment_method' => $payment->payment_method ?? 'Cash',
                    'notes' => $payment->notes,
                    'recorded_by' => $payment->recorder->name ?? 'N/A',
                    'payment_date' => $payment->payment_date->format('M d, Y h:i A'),
                    'created_at' => $payment->created_at->format('M d, Y h:i A'),
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Release/Cancel a table booking (marks as released but keeps record for tracking)
     */
    public function releaseTable(Request $request, $bookingId): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            // Check permissions
            if (auth()->user()->role === 'employee' && $booking->employee_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to release this booking.'
                ], 403);
            }
            
            // Check if already released
            if ($booking->released_at !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'This table has already been released.'
                ], 400);
            }
            
            // Mark as released (keep the record for tracking)
            $booking->update([
                'released_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Table released successfully! The booking record has been kept for tracking purposes.'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
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
