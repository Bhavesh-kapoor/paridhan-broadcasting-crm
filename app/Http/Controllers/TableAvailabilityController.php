<?php

namespace App\Http\Controllers;

use App\Models\LocationMngt;
use App\Models\LocationMngtTableDetail;
use App\Models\Booking;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class TableAvailabilityController extends Controller
{
    /**
     * Display table availability for all locations with campaigns
     */
    public function index(): View
    {
        // Don't load all locations at once - use AJAX search instead
        // Only load a few initial locations for quick start
        
        // Get active campaigns for employees
        $campaigns = \App\Models\Campaign::where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get exhibitors and visitors for dropdowns
        $exhibitors = \App\Models\Contacts::where('type', 'exhibitor')->orderBy('name')->get();
        $visitors = \App\Models\Contacts::where('type', 'visitor')->orderBy('name')->get();
        
        return view('table-availability.index', compact('campaigns', 'exhibitors', 'visitors'));
    }

    /**
     * Search locations with pagination (AJAX)
     */
    public function searchLocations(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 12); // 12 locations per page
        
        $query = LocationMngt::query();
        
        // Add search functionality
        if (!empty($search)) {
            $query->where('loc_name', 'LIKE', '%' . $search . '%');
        }
        
        // Get paginated results
        $locations = $query->orderBy('loc_name')
            ->paginate($perPage, ['*'], 'page', $page);
        
        // Format response
        $data = $locations->map(function($location) {
            return [
                'id' => $location->id,
                'name' => $location->loc_name,
                'tables_count' => $location->tables()->count(),
            ];
        });
        
        return response()->json([
            'status' => true,
            'data' => $data,
            'current_page' => $locations->currentPage(),
            'last_page' => $locations->lastPage(),
            'per_page' => $locations->perPage(),
            'total' => $locations->total(),
            'has_more' => $locations->hasMorePages(),
        ]);
    }

    /**
     * Get table availability for a specific location
     */
    public function getLocationTables($locationId): JsonResponse
    {
        $location = LocationMngt::with('tables')->findOrFail($locationId);
        
        // Get all bookings for this location
        $bookings = Booking::where('location_id', $locationId)
            ->whereNotNull('table_id')
            ->with(['visitor', 'exhibitor', 'employee'])
            ->get();
        
        // Get all conversations for this location
        $conversations = Conversation::where('location_id', $locationId)
            ->whereNotNull('table_id')
            ->with(['visitor', 'exhibitor', 'employee'])
            ->get();
        
        // Combine bookings and conversations to mark tables as used
        $usedTableIds = $bookings->pluck('table_id')->merge($conversations->pluck('table_id'))->unique();
        
        $tables = $location->tables->map(function($table) use ($usedTableIds, $bookings, $conversations) {
            $isUsed = $usedTableIds->contains($table->id);
            
            // Get booking/conversation info if used
            $booking = $bookings->firstWhere('table_id', $table->id);
            $conversation = $conversations->firstWhere('table_id', $table->id);
            
            return [
                'id' => $table->id,
                'table_no' => $table->table_no,
                'table_size' => $table->table_size,
                'price' => $table->price,
                'is_used' => $isUsed,
                'booking' => $booking ? [
                    'visitor_name' => $booking->visitor->name ?? 'N/A',
                    'exhibitor_name' => $booking->exhibitor->name ?? 'N/A',
                    'employee_name' => $booking->employee->name ?? 'N/A',
                    'booking_date' => $booking->booking_date->format('Y-m-d'),
                    'amount_paid' => $booking->amount_paid,
                ] : null,
                'conversation' => $conversation ? [
                    'visitor_name' => $conversation->visitor->name ?? $conversation->visitor_phone ?? 'N/A',
                    'exhibitor_name' => $conversation->exhibitor->name ?? 'N/A',
                    'employee_name' => $conversation->employee->name ?? 'N/A',
                    'outcome' => $conversation->outcome,
                    'conversation_date' => $conversation->conversation_date->format('Y-m-d H:i'),
                ] : null,
            ];
        });
        
        return response()->json([
            'status' => true,
            'location' => [
                'id' => $location->id,
                'name' => $location->loc_name,
            ],
            'tables' => $tables,
            'total_tables' => $tables->count(),
            'used_tables' => $tables->where('is_used', true)->count(),
            'available_tables' => $tables->where('is_used', false)->count(),
        ]);
    }

    /**
     * Create conversation from table availability
     */
    public function createConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campaign_id' => 'nullable|ulid|exists:campaigns,id',
            'exhibitor_id' => 'required|ulid|exists:contacts,id',
            'visitor_id' => 'nullable|ulid|exists:contacts,id',
            'visitor_phone' => 'nullable|string|max:20',
            'location_id' => 'required|exists:location_mngt,id',
            'table_id' => 'required|exists:location_mngt_table_details,id',
            'outcome' => 'required|in:busy,interested,materialised',
            'notes' => 'nullable|string|max:2000',
            'conversation_date' => 'nullable|date',
        ]);

        try {
            $validated['employee_id'] = auth()->id();
            $validated['conversation_date'] = $validated['conversation_date'] ?? now();

            // Find campaign recipient if campaign_id and visitor_id are provided
            if (!empty($validated['campaign_id']) && !empty($validated['visitor_id'])) {
                $campaignRecipient = \App\Models\CampaignRecipient::where('campaign_id', $validated['campaign_id'])
                    ->where('contact_id', $validated['visitor_id'])
                    ->first();
                if ($campaignRecipient) {
                    $validated['campaign_recipient_id'] = $campaignRecipient->id;
                }
            }

            $conversationService = app(\App\Services\ConversationService::class);
            $conversation = $conversationService->create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Conversation added successfully!',
                'data' => $conversation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create booking from table availability
     */
    public function createBooking(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campaign_id' => 'nullable|ulid|exists:campaigns,id',
            'exhibitor_id' => 'required|ulid|exists:contacts,id',
            'visitor_id' => 'nullable|ulid|exists:contacts,id',
            'phone' => 'nullable|string|max:20',
            'location_id' => 'required|exists:location_mngt,id',
            'table_id' => 'required|exists:location_mngt_table_details,id',
            'booking_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'amount_status' => 'required|in:paid,partial,pending',
        ]);

        try {
            $validated['employee_id'] = auth()->id();
            
            // Get table to set backward compatibility fields
            $table = LocationMngtTableDetail::find($validated['table_id']);
            $location = LocationMngt::find($validated['location_id']);
            
            $validated['booking_location'] = $location->loc_name ?? null;
            $validated['table_no'] = $table->table_no ?? null;
            
            // Create booking
            $booking = Booking::create($validated);
            
            // Create conversation if campaign_id exists
            if (!empty($validated['campaign_id'])) {
                $conversationService = app(\App\Services\ConversationService::class);
                $conversationService->createFromBooking($booking);
            }

            return response()->json([
                'status' => true,
                'message' => 'Table booked successfully!',
                'data' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }
}
