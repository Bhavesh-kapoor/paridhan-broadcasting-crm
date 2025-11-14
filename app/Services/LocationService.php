<?php

namespace App\Services;

use App\Models\LocationMngt;
use App\Models\LocationMngtTableDetail;
use Illuminate\Support\Facades\DB;
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

    /**
     * Create a new LocationMngt
     */
    // public function createLocation($data)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $imageName = null;

    //         if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
    //             $file = $data['image'];
    //             $imageName = time() . '_' . $file->getClientOriginalName();
    //             $file->move(public_path('uploads/location_images'), $imageName);
    //         }

    //         dd($imageName);

    //         $location = LocationMngt::create([
    //             'loc_name' => $data['loc_name'],
    //             'type'     => $data['type'],
    //             'address'  => $data['address'] ?? null,
    //             'status'   => $data['status'],
    //             'image'    => $imageName, // this will now save correctly
    //         ]);

    //         // Insert table details
    //         if (!empty($data['tables']) && is_array($data['tables'])) {
    //             foreach ($data['tables'] as $table) {
    //                 LocationMngtTableDetail::create([
    //                     'location_mngt_id' => $location->id,
    //                     'table_no'         => $table['table_no'] ?? null,
    //                     'table_size'       => $table['table_size'] ?? null,
    //                     'price'            => $table['price'] ?? null,
    //                 ]);
    //             }
    //         }

    //         DB::commit();
    //         return [
    //             'success' => true,
    //             'message' => 'Location created successfully!',
    //             'data'    => $location
    //         ];
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return [
    //             'success' => false,
    //             'message' => 'Failed to create location: ' . $e->getMessage()
    //         ];
    //     }
    // }

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
                'success' => true,
                'message' => 'Location created successfully!',
                'data'    => $location
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
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
                'status'   => $request->status,
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
                'success' => true,
                'message' => 'Location updated successfully!',
                'data'    => $location
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
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
            $location = LocationMngt::findOrFail($id);

            LocationMngtTableDetail::where('location_mngt_id', $id)->delete();
            $location->delete();

            DB::commit();
            return [
                'success' => true,
                'message' => 'Location deleted successfully!'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to delete location: ' . $e->getMessage()
            ];
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
