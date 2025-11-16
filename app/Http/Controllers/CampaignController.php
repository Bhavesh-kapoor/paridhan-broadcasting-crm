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
     * server side rendoring data table
     */
    public function getAllCampaignsList(Request $request)
    {
        return $this->campaignService->getAllCampaignsList();
    }

    public function getAllCampaignsRecipientsList(Request $request)
    {
        return $this->campaignService->getAllCampaignsRecipientsList($request->input('id'));
    }




    public function ajaxExhibitors(Request $request)
    {
        $exhibitors = $this->campaignService->getContactsForCampaign('exhibitor');

        return view('campaigns.partials.exhibitors', compact('exhibitors'))->render();
    }

    public function ajaxVisitors(Request $request)
    {
        $visitors = $this->campaignService->getContactsForCampaign('visitor');

        return view('campaigns.partials.visitors', compact('visitors'))->render();
    }


    public function getAllExhibitorIDs()
    {
        return $this->campaignService->getAllRecipientsIDs('exhibitor');
    }

    public function getAllVisitorIDs()
    {
        return $this->campaignService->getAllRecipientsIDs('visitor');
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
            $this->campaignService->createCampaign($request->validated());
            return response()->json([
                'status' => true,
                'message' => 'Campaign created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
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
            $this->campaignService->updateCampaign($id, $request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Campaign updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
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
                'status' => true,
                'message' => 'Campaign deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
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
            $this->campaignService->sendCampaign($id);

            return response()->json([
                'status' => true,
                'message' => 'Campaign sent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
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
            'status' => true,
            'contacts' => $contacts
        ]);
    }
}
