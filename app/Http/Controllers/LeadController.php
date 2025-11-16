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

    public function getAllLeadsList()
    {
        return $this->service->getAllLeadsList();
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

    // Get Tables by Location ID
    public function getTables($locationId)
    {
        $tables = LocationMngtTableDetail::where('id', $locationId)->get();
        return response()->json($tables);
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


    public function searchLocation(Request $request)
    {
        $term = $request->get('term');
        $locations = LocationMngt::where('loc_name', 'like', '%' . $term . '%')
            ->orWhere('address', 'like', '%' . $term . '%')
            ->select('id', DB::raw("CONCAT(loc_name, ' - ', address) AS text"))
            ->limit(20)
            ->get();

        return response()->json($locations);
    }
}
