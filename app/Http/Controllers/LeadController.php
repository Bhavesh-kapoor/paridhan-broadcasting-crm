<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FollowUpService;
use App\Http\Requests\FollowUpRequest;
use Illuminate\Http\JsonResponse;




class LeadController extends Controller
{
    protected $service;

    public function __construct(FollowUpService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return view('leads.index');
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
}
