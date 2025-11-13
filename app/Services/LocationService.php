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
    public function createLocation($data)
    {
        DB::beginTransaction();

        try {
            //  Create main location record
            $location = LocationMngt::create([
                'loc_name' => $data['loc_name'],
                'type'     => $data['type'],
                'address'  => $data['address'] ?? null,
                'status'   => $data['status'],
            ]);

            //  Insert child table details (if any)
            if (!empty($data['tables']) && is_array($data['tables'])) {
                foreach ($data['tables'] as $table) {
                    LocationMngtTableDetail::create([
                        'location_mngt_id' => $location->id,
                        'table_no'         => $table['table_no'] ?? null,
                        'table_size'       => $table['table_size'] ?? null,
                        'price'            => $table['price'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return $location;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing LocationMngt
     */
    public function updateLocation($id, $data)
    {
        DB::beginTransaction();

        try {
            $location = LocationMngt::findOrFail($id);

            $location->update([
                'loc_name' => $data['loc_name'],
                'type'     => $data['type'],
                'address'  => $data['address'] ?? null,
                'status'   => $data['status'],
            ]);

            // Delete old tables
            LocationMngtTableDetail::where('location_mngt_id', $id)->delete();

            // Reinsert new tables
            if (!empty($data['tables'])) {
                foreach ($data['tables'] as $table) {
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
