<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Conversation;
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
     * Generate invoice for a booking
     */
    public function generate($bookingId): View
    {
        $booking = Booking::with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign'])
            ->findOrFail($bookingId);
        
        return view('invoices.booking', compact('booking'));
    }

    /**
     * Generate invoice from a conversation (if it has a booking)
     */
    public function generateFromConversation($conversationId): View
    {
        $conversation = Conversation::with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'booking'])
            ->findOrFail($conversationId);
        
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
        return view('bookings.employee_index');
    }

    /**
     * Get employee bookings for DataTable (AJAX)
     */
    public function getEmployeeBookings(Request $request): JsonResponse
    {
        $employeeId = auth()->id();
        
        $bookings = Booking::where('employee_id', $employeeId)
            ->with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'conversation'])
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
                'conversation_id' => $booking->conversation->id ?? null,
                'created_at' => $booking->created_at->format('M d, Y'),
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }
}
