<?php

namespace App\Http\Controllers;

use App\Services\EmployeeService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $employeeService;
    
    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }
    
    public function index(){
        $employeeStats = $this->employeeService->getEmployeeStats();
        return view("dashboard", compact('employeeStats'));
    }
}
