<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use App\Models\LocationMngt;
use App\Models\LocationMngtTableDetail;
use Yajra\DataTables\Facades\DataTables;



use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('locations.index');
    }

    // datatables ajax function
    public function getLocations(Request $request)
    {
        if ($request->ajax()) {
            $query = LocationMngt::select(['id', 'loc_name', 'type', 'status', 'address']);

            //  Apply filter if status selected
            if (!empty($request->status)) {
                $query->where('type', $request->status);
            }

            // dd($query);
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    return '<span class="badge bg-' .
                        ($row->status == 'active' ? 'success' : 'danger') .
                        '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('actions', function ($row) {
                    return '
                      <button class="btn btn-info btn-sm viewLocation" data-id="' . $row->id . '">
                        <i class="ph ph-eye"></i>
                      </button>

                    <a href="' . route('locations.edit', $row->id) . '" class="btn btn-primary btn-sm"><i class="ph ph-pencil"></i></a>
                    <button class="btn btn-danger btn-sm deleteLocation" data-id="' . $row->id . '"><i class="ph ph-trash"></i></button>
                ';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'visitor');
        $title = 'Add ' . ucfirst($type);

        return view('locations.create', compact('type', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationRequest $request): JsonResponse
    {
        try {
            // Pass all validated + table data to the service
            $location = $this->locationService->createLocation($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Location and tables added successfully!',
                'redirect' => route('locations.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $location = LocationMngt::findOrFail($id);
        $tableDetails = LocationMngtTableDetail::where('location_mngt_id', $id)->get();
        $title = 'View ' . ucfirst($location->type);

        return view('locations.show', compact('location', 'title', 'tableDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $location = $this->locationService->getLocationById($id);
        $loc_name = $location->loc_name;
        $title = 'Edit ' . ucfirst($loc_name);

        return view('locations.edit', compact('location', 'loc_name', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request, $id): JsonResponse
    {
        try {
            $update = $this->locationService->updateLocation($id, $request->validated());

            if ($update['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $update['message'],
                    'redirect' => route('locations.index')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $update['message']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating location: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            // Find location (throws 404 if not found)
            $location = LocationMngt::findOrFail($id);

            // Delete related tables first
            LocationMngtTableDetail::where('location_mngt_id', $id)->delete();

            // Delete main location
            $location->delete();

            return response()->json([
                'success' => true,
                'message' => ucfirst($location->type) . ' deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete location: ' . $e->getMessage(),
            ], 500);
        }
    }
}
