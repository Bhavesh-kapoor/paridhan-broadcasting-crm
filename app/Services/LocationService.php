<?php

namespace App\Services;

use App\Models\LocationMngt;
use App\Models\LocationMngtTableDetail;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;

class LocationService
{

    /**
     * Get LocationMngt by ID
     */
    public function getLocationById($id)
    {
        return LocationMngt::with('tables')->findOrFail($id);
    }

    public function getAllLocationsList()
    {
        $result =  DB::table('location_mngt')   ///raw query builder
            ->orderByDesc('id');
        return DataTables::of($result)
            ->addIndexColumn()
            ->addColumn('action', function ($location) {
                $id = $location->id;
                $button = '<button class="btn btn-success btn-sm showBtn" editRoute="' . route('locations.edit', $id) . '"  data-bs-toggle="tooltip" data-bs-placement="left" title="View Details">
                <i class="bx bx-show"></i>
                </button> <button class="btn btn-primary btn-sm editBtn" editRoute="' . route('locations.edit', $id) . '" updateRoute="' . route('locations.update', $id) . '"  data-bs-toggle="tooltip" data-bs-placement="left" title="Edit location">
                <i class="bx bx-pencil"></i>
                </button>  <button class="btn btn-danger btn-sm deleteBtn" id="' . $id . '" data-bs-toggle="tooltip" data-bs-placement="left" title="Delete location">
                    <i class="bx bx-trash"></i>
                </button>';
                return $button;
            })
            ->addColumn('image', function ($row) {

                // If image exists, use it — otherwise use dummy image
                $imagePath = $row->image
                    ? 'uploads/location_images/' . $row->image
                    : 'uploads/location_images/dummy.png';   // <--- dummy image

                $url = asset($imagePath);

                return '
                       <a href="' . $url . '" target="_blank">
                              <img src="' . $url . '" width="50" height="50"
                                style="border-radius:5px; object-fit:cover;">
                         </a>
                    ';
            })
            ->rawColumns(['action', 'image'])
            ->make(true);
    }

    public function createLocation($request)
    {
        DB::beginTransaction();

        try {
            $imageName = null;

            // Handle file upload correctly
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/location_images'), $imageName);
            }


            $location = LocationMngt::create([
                'loc_name' => $request->loc_name,
                'type'     => $request->type,
                'address'  => $request->address ?? null,
                'status'   => $request->status,
                'image'    => $imageName,
            ]);

            if (!empty($request->tables) && is_array($request->tables)) {
                foreach ($request->tables as $table) {
                    LocationMngtTableDetail::create([
                        'location_mngt_id' => $location->id,
                        'table_no'         => $table['table_no'] ?? null,
                        'table_size'       => $table['table_size'] ?? null,
                        'price'            => $table['price'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return [
                'status' => true,
                'message' => 'Location created successfully!',
                'data'    => $location
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Failed to create location: ' . $e->getMessage()
            ];
        }
    }


    /**
     * Update an existing LocationMngt
     */
    public function updateLocation($id, $request)
    {
        DB::beginTransaction();

        try {
            $location = LocationMngt::findOrFail($id);

            // Keep the old image by default
            $imageName = $location->image;

            // Handle new image upload if provided
            if ($request->hasFile('image') && $request->file('image')->isValid()) {

                $file = $request->file('image');

                // Validate MIME type
                $allowedMime = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($file->getClientMimeType(), $allowedMime)) {
                    throw new \Exception('Only JPG, JPEG & PNG files are allowed.');
                }

                // Validate file size (max 5MB)
                if ($file->getSize() > 5 * 1024 * 1024) {
                    throw new \Exception('Image size cannot exceed 5MB.');
                }

                // Delete old image if exists
                if ($imageName && file_exists(public_path('uploads/location_images/' . $imageName))) {
                    unlink(public_path('uploads/location_images/' . $imageName));
                }

                // Save new image
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/location_images'), $imageName);
            }

            // Update main location record
            $location->update([
                'loc_name' => $request->loc_name,
                'type'     => $request->type,
                'address'  => $request->address ?? null,
                'image'    => $imageName,
            ]);

            // Delete old table details
            LocationMngtTableDetail::where('location_mngt_id', $id)->delete();

            // Insert new table details if provided
            if (!empty($request->tables) && is_array($request->tables)) {
                foreach ($request->tables as $table) {
                    LocationMngtTableDetail::create([
                        'location_mngt_id' => $location->id,
                        'table_no'         => $table['table_no'] ?? null,
                        'table_size'       => $table['table_size'] ?? null,
                        'price'            => $table['price'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return [
                'status' => true,
                'message' => 'Location updated successfully!',
                'data'    => $location
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => 'Failed to update location: ' . $e->getMessage()
            ];
        }
    }



    /**
     * Delete a LocationMngt
     */
    public function deleteLocation($id)
    {
        DB::beginTransaction();

        try {
            $location = $this->getLocationById($id);
            // delete table details first
            LocationMngtTableDetail::where('location_mngt_id', $id)->delete();

            // delete location
            $location->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    /**
     * Get Location statistics
     */
    public function getStats()
    {
        return [
            'total'   => LocationMngt::count(),
            'active'  => LocationMngt::where('status', 'active')->count(),
            'inactive' => LocationMngt::where('status', 'inactive')->count(),
            'types'   => LocationMngt::select('type', DB::raw('COUNT(*) as total'))->groupBy('type')->get(),
        ];
    }
}
