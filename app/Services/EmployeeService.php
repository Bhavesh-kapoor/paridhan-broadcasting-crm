<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class EmployeeService
{
    /**
     * Get all employees with optional filters
     */
    public function getAllEmployees($filters = [])
    {
        $query = User::employees();

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->inactive();
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }


    public function getAllEmployeesList(array $filters = [])
    {
        $query = user::where('role', 'employee');
        // // Apply status filter
        if (!empty($filters['filter_status'])) {
            if ($filters['filter_status'] === 'active') {
                $query->active();
            } elseif ($filters['filter_status'] === 'inactive') {
                $query->inactive();
            }
        }
        $result = $query->orderBy('created_at', 'desc');
        // dd($result);

        return DataTables::of($result)
            ->addIndexColumn()
            ->addColumn('action', function ($employee) {
                $id = $employee->id;
                $status = $employee->status;
                $button_class = 'success';
                $button_icon_class = 'pause';
                $button_text = 'Deactivate';
                if ($status === 'inactive') {
                    $button_class = 'warning';
                    $button_icon_class = 'play';
                    $button_text = 'Activate';
                }

                $button = '<div class="d-flex gap-1 justify-content-center">';
                
                $button .= '<button class="btn btn-action btn-edit editBtn" editRoute="' . route('employees.edit', $id) . '" updateRoute="' . route('employees.update', $id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Employee">
                    <i class="bx bx-edit"></i>
                    <span class="d-none d-md-inline">Edit</span>
                </button>';
                
                $button .= '<button class="btn btn-action btn-' . $button_class . ' deactivateBtn" id="' . $id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $button_text . ' Employee">
                    <i class="bx bx-' . $button_icon_class . '"></i>
                    <span class="d-none d-md-inline">' . $button_text . '</span>
                </button>';
                
                $button .= '<button class="btn btn-action btn-delete deleteBtn" id="' . $id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Employee">
                    <i class="bx bx-trash"></i>
                    <span class="d-none d-md-inline">Delete</span>
                </button>';
                
                $button .= '</div>';
                return $button;
            })


            ->addColumn('full_status', function ($employee) {
                $status = $employee->status;
                $button_class = 'success';
                $button_icon_class = 'check-circle';
                if ($status === 'inactive') {
                    $button_icon_class = 'pause-circle';
                    $button_class = 'warning';
                }

                return ' <span
                                                    class="badge bg-' . $button_class . ' px-3 py-2">
                                                    <i
                                                        class="bx bx-' . $button_icon_class . ' me-1"></i>
                                                   ' . ucfirst($employee->status) . '
                                                </span>';
            })
            ->rawColumns(['action', 'full_status'])
            ->make(true);
    }
    /**
     * Get employee by ID
     */
    public function getEmployeeById($id)
    {
        return User::employees()->findOrFail($id);
    }

    /**
     * Create a new employee
     */
    public function createEmployee($data)
    {
        DB::beginTransaction();

        try {
            $employee = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'role' => 'employee',
                'status' => 'active',
                'position' => $data['position'] ?? null,
                'salary' => $data['salary'] ?? null,
                'hire_date' => $data['hire_date'] ?? now(),
            ]);

            DB::commit();
            return $employee;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing employee
     */
    public function updateEmployee($id, $data)
    {
        DB::beginTransaction();

        try {
            $employee = $this->getEmployeeById($id);
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'position' => $data['position'] ?? null,
                'salary' => $data['salary'] ?? null,
                'hire_date' => $data['hire_date'] ?? null,
            ];

            // Only update password if provided
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $employee->update($updateData);

            DB::commit();
            return $employee;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete an employee
     */
    public function deleteEmployee($id)
    {
        DB::beginTransaction();

        try {
            $employee = $this->getEmployeeById($id);
            $employee->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle employee status
     */
    public function toggleStatus($id)
    {
        DB::beginTransaction();

        try {
            $employee = $this->getEmployeeById($id);
            $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
            $employee->update(['status' => $newStatus]);

            DB::commit();
            return $employee;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Change employee password
     */
    public function changePassword($id, $currentPassword, $newPassword)
    {
        DB::beginTransaction();

        try {
            $employee = $this->getEmployeeById($id);

            // Verify current password
            if (!Hash::check($currentPassword, $employee->password)) {
                throw new \Exception('Current password is incorrect.');
            }

            // Update password
            $employee->update(['password' => Hash::make($newPassword)]);

            DB::commit();
            return $employee;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get employee statistics
     */
    public function getEmployeeStats()
    {
        return [
            'total' => User::employees()->count(),
            'active' => User::active()->count(),
            'inactive' => User::inactive()->count(),
        ];
    }
}
