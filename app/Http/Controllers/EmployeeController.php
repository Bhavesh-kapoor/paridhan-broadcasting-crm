<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $employees = $this->employeeService->getAllEmployees($filters);
        
        return view('employees.index', compact('employees', 'filters'));
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created employee
     */
    public function store(EmployeeRequest $request): JsonResponse
    {
        try {
            $employee = $this->employeeService->createEmployee($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!',
                'redirect' => route('employees.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified employee
     */
    public function show($id)
    {
        $employee = $this->employeeService->getEmployeeById($id);
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee
     */
    public function edit($id)
    {
        $employee = $this->employeeService->getEmployeeById($id);
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee
     */
    public function update(EmployeeRequest $request, $id): JsonResponse
    {
        try {
            $employee = $this->employeeService->updateEmployee($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully!',
                'redirect' => route('employees.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified employee
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->employeeService->deleteEmployee($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle employee status
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $employee = $this->employeeService->toggleStatus($id);
            $newStatus = $employee->status;
            
            return response()->json([
                'success' => true,
                'message' => "Employee status changed to {$newStatus}!",
                'newStatus' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show change password form
     */
    public function showChangePassword($id)
    {
        $employee = $this->employeeService->getEmployeeById($id);
        return view('employees.change-password', compact('employee'));
    }

    /**
     * Change employee password
     */
    public function changePassword(ChangePasswordRequest $request, $id): JsonResponse
    {
        try {
            $this->employeeService->changePassword(
                $id,
                $request->current_password,
                $request->new_password
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully!',
                'redirect' => route('employees.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password: ' . $e->getMessage()
            ], 500);
        }
    }
}
