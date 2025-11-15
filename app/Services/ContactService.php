<?php

namespace App\Services;

use App\Models\Contacts;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ContactService
{
    /**
     * Get all contacts with optional filters
     */
    public function getAllContacts($type = 'visitor', $filters = [])
    {
        $query = Contacts::where('type', $type);

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Apply location filter
        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }


    public function getAllContactsList($type = 'visitor')
    {
        $result =  DB::table('contacts')
            ->where('type', $type)
            ->orderByDesc('id');
        return DataTables::of($result)
            ->addIndexColumn()
            ->addColumn('action', function ($employee) {
                $id = $employee->id;
                $button = ' <button class="btn btn-primary btn-sm editBtn" editRoute="' . route('contacts.edit', $id) . '" updateRoute="' . route('contacts.update', $id) . '"  data-bs-toggle="tooltip" data-bs-placement="left" title="Edit Employee">
                <i class="bx bx-pencil"></i>
                </button>  <button class="btn btn-danger btn-sm deleteBtn" id="' . $id . '" data-bs-toggle="tooltip" data-bs-placement="left" title="Delete Employee">
                    <i class="bx bx-trash"></i>
                </button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Get contact by ID
     */
    public function getContactById($id)
    {
        return Contacts::findOrFail($id);
    }

    /**
     * Create a new contact
     */
    public function createContact($data)
    {
        DB::beginTransaction();

        try {
            $contact = Contacts::create([
                'name' => $data['name'],
                'location' => $data['location'],
                'phone' => $data['phone'],
                'type' => $data['type'],
                'email' => $data['email'] ?? null,
                'alternate_phone' => $data['alternate_phone'] ?? null,
                'product_type' => $data['product_type'] ?? null,
                'brand_name' => $data['brand_name'] ?? null,
                'business_type' => $data['business_type'] ?? null,
                'gst_number' => $data['gst_number'] ?? null,
            ]);

            DB::commit();
            return $contact;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing contact
     */
    public function updateContact($id, $data)
    {
        DB::beginTransaction();

        try {
            $contact = $this->getContactById($id);

            $updateData = [
                'name' => $data['name'],
                'location' => $data['location'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'alternate_phone' => $data['alternate_phone'] ?? null,
                'product_type' => $data['product_type'] ?? null,
                'brand_name' => $data['brand_name'] ?? null,
                'business_type' => $data['business_type'] ?? null,
                'gst_number' => $data['gst_number'] ?? null,
            ];

            $contact->update($updateData);

            DB::commit();
            return $contact;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a contact
     */
    public function deleteContact($id)
    {
        DB::beginTransaction();

        try {
            $contact = $this->getContactById($id);
            $contact->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get contact statistics
     */
    public function getContactStats($type = null)
    {
        $query = Contacts::query();

        if ($type) {
            $query->where('type', $type);
        }

        return [
            'total' => $query->count(),
            'this_month' => $query->where('created_at', '>=', now()->startOfMonth())->count(),
            'this_week' => $query->where('created_at', '>=', now()->startOfWeek())->count(),
            'today' => $query->where('created_at', '>=', now()->startOfDay())->count(),
        ];
    }

    /**
     * Get contacts by type
     */
    public function getContactsByType($type)
    {
        return Contacts::where('type', $type)->orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * Search contacts
     */
    public function searchContacts($type, $searchTerm)
    {
        return Contacts::where('type', $type)
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%")
                    ->orWhere('location', 'like', "%{$searchTerm}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}
