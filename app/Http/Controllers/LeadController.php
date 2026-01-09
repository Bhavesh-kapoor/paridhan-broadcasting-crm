<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FollowUpService;
use App\Http\Requests\FollowUpRequest;
use App\Models\Booking;
use App\Models\LocationMngt;
use App\Models\LocationMngtTableDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    protected $service;

    public function __construct(FollowUpService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $location = LocationMngt::all();
        return view('leads.index', compact('location'));
    }

    public function getAllLeadsList(Request $request)
    {
        $filters = $request->only(['filter_lead_type']);
        return $this->service->getAllLeadsList($filters);
    }


    // store follow-up
    public function store(FollowUpRequest $request)
    {
        $this->service->create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Follow-up added successfully!'
        ]);
    }


    public function edit($phone): JsonResponse
    {
        try {
            $lead = $this->service->getFollowUpData($phone);
            return response()->json([
                'status' => true,
                'message' => 'Lead fetched successfully',
                'data' => $lead
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching lead: ' . $e->getMessage()
            ], 500);
        }
    }



    public function getFollowUps($phone, FollowUpService $service)
    {
        $data = $service->getFollowUps($phone);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Search contact by phone number
     */
    public function searchContactByPhone(Request $request): JsonResponse
    {
        $phone = $request->input('phone');
        
        if (empty($phone)) {
            return response()->json([
                'status' => false,
                'message' => 'Phone number is required'
            ], 400);
        }

        $contact = \App\Models\Contacts::where('phone', $phone)->first();

        if ($contact) {
            return response()->json([
                'status' => true,
                'contact' => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'phone' => $contact->phone,
                    'type' => $contact->type,
                ]
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Contact not found'
        ]);
    }

    // Get Tables by Location ID (with search and pagination support)
    public function getTables($locationId, Request $request)
    {
        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $perPage = 50; // Load 50 tables per page for Select2
            
            // Check if locationId is numeric (ID) or string (name)
            if (is_numeric($locationId)) {
                $query = LocationMngtTableDetail::where('location_mngt_id', $locationId);
            } else {
                // Try to find location by name first
                $location = LocationMngt::where('loc_name', $locationId)->first();
                if ($location) {
                    $query = LocationMngtTableDetail::where('location_mngt_id', $location->id);
                } else {
                    return response()->json(['results' => [], 'pagination' => ['more' => false]]);
                }
            }
            
            // Add search functionality
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('table_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('id', 'LIKE', '%' . $search . '%');
                });
            }
            
            // Check if this is a Select2 AJAX request
            if ($request->has('search') || $request->has('page')) {
                $total = $query->count();
                $tables = $query->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get();
                
                $results = $tables->map(function($table) {
                    // Check if table is booked or in use (exclude released bookings)
                    $isBooked = Booking::where('table_id', $table->id)
                        ->where('booking_date', '>=', now()->toDateString())
                        ->whereNull('released_at') // Exclude released bookings
                        ->exists();
                    $isInUse = \App\Models\Conversation::where('table_id', $table->id)
                        ->where('conversation_date', '>=', now()->subDays(1))
                        ->exists();
                    $status = ($isBooked || $isInUse) ? ' (Unavailable)' : '';
                    
                    return [
                        'id' => $table->id,
                        'text' => $table->table_no . ' - ₹' . number_format($table->price ?? 0, 2) . ($table->table_size ? ' (' . $table->table_size . ')' : '') . $status,
                        'table_no' => $table->table_no,
                        'price' => $table->price ?? 0,
                        'table_size' => $table->table_size ?? null,
                        'available' => !($isBooked || $isInUse),
                    ];
                })->values()->all();
                
                return response()->json([
                    'results' => $results,
                    'pagination' => [
                        'more' => ($page * $perPage) < $total
                    ]
                ]);
            }
            
            // Regular request - return all tables (limited to first 500 for performance)
            $tables = $query->take(500)->get();
            
            return response()->json($tables->map(function($table) {
                return [
                    'id' => $table->id,
                    'table_no' => $table->table_no,
                    'table_name' => $table->table_no ?? ('Table ' . $table->id),
                    'price' => $table->price ?? 0,
                    'table_size' => $table->table_size ?? null,
                ];
            })->values()->all());
        } catch (\Exception $e) {
            \Log::error('Error loading tables: ' . $e->getMessage());
            return response()->json(['results' => [], 'pagination' => ['more' => false]], 500);
        }
    }

    // Get Price by Table ID
    public function getPrice($tableId)
    {
        $table = LocationMngtTableDetail::find($tableId);

        if (!$table) {
            return response()->json(['price' => 0]);
        }

        return response()->json(['price' => $table->price]);
    }


    // Table Availability Check
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'booking_date' => 'required|date',
            'booking_location' => 'required|string',
            'table_no' => 'required|string',
            'price' => 'required|numeric',
        ]);

        // Check if row already exists
        $exists = Booking::where('booking_date', $request->booking_date)
            ->where('booking_location', $request->booking_location)
            ->where('table_no', $request->table_no)
            ->where('price', $request->price)
            ->exists();

        if ($exists) {
            return response()->json(['available' => false]);
        }

        return response()->json(['available' => true]);
    }


    // public function searchLocation(Request $request)
    // {
    //     $term = $request->get('term');
    //     $locations = LocationMngt::where('loc_name', 'like', '%' . $term . '%')
    //         ->orWhere('address', 'like', '%' . $term . '%')
    //         ->select('id', DB::raw("CONCAT(loc_name, ' - ', address) AS text"))
    //         ->limit(20)
    //         ->get();

    //     return response()->json($locations);
    // }
}
