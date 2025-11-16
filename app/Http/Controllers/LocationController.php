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


    public function getAllLocationsList(Request $request): JsonResponse
    {
        $type = $request->type;
        return $this->locationService->getAllLocationsList($type);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationRequest $request): JsonResponse
    {
        // dd($request->all());
        try {
            $location = $this->locationService->createLocation($request);

            if ($location['status'] === false) {
                return response()->json([
                    'status' => false,
                    'message' => $location['message']
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Location and tables added successfully!',
                'data'    => $location['data']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create location: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): JsonResponse
    {
        try {
            $location = $this->locationService->getLocationById($id);
            // dd($location);
            return response()->json([
                'status' => true,
                'message' => 'Location fetched successfully',
                'data' => $location
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request, $id): JsonResponse
    {
        try {
            // Pass the full $request object, not $request->validated()
            $update = $this->locationService->updateLocation($id, $request);

            if ($update['status']) {
                return response()->json([
                    'status' => true,
                    'message' => $update['message'],
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $update['message']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
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
            $location = $this->locationService->deleteLocation($id);


            return response()->json([
                'status' => true,
                'message' => 'deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete contact: ' . $e->getMessage()
            ], 500);
        }
    }
}
