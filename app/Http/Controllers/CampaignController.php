<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * Display a listing of campaigns
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $campaigns = $this->campaignService->getAllCampaigns($filters);
        
        return view('campaigns.index', compact('campaigns', 'filters'));
    }

    /**
     * Show the form for creating a new campaign
     */
    public function create()
    {
        $exhibitors = $this->campaignService->getContactsForCampaign('exhibitor');
        $visitors = $this->campaignService->getContactsForCampaign('visitor');
        
        return view('campaigns.create', compact('exhibitors', 'visitors'));
    }

    /**
     * Store a newly created campaign
     */
    public function store(CampaignRequest $request): JsonResponse
    {
        try {
            $campaign = $this->campaignService->createCampaign($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Campaign created successfully!',
                'redirect' => route('campaigns.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified campaign
     */
    public function show($id)
    {
        $campaign = $this->campaignService->getCampaignById($id);
        return view('campaigns.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified campaign
     */
    public function edit($id)
    {
        $campaign = $this->campaignService->getCampaignById($id);
        $exhibitors = $this->campaignService->getContactsForCampaign('exhibitor');
        $visitors = $this->campaignService->getContactsForCampaign('visitor');
        
        return view('campaigns.edit', compact('campaign', 'exhibitors', 'visitors'));
    }

    /**
     * Update the specified campaign
     */
    public function update(CampaignRequest $request, $id): JsonResponse
    {
        try {
            $campaign = $this->campaignService->updateCampaign($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Campaign updated successfully!',
                'redirect' => route('campaigns.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified campaign
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->campaignService->deleteCampaign($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Campaign deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send campaign
     */
    public function send($id): JsonResponse
    {
        try {
            $campaign = $this->campaignService->sendCampaign($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Campaign sent successfully!',
                'redirect' => route('campaigns.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contacts for campaign selection (AJAX)
     */
    public function getContacts(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $contacts = $this->campaignService->getContactsForCampaign($type);
        
        return response()->json([
            'success' => true,
            'contacts' => $contacts
        ]);
    }
}
