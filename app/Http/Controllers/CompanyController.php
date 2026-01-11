<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Services\CompanyDashboardService;
use App\Services\ConversationService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $dashboardService;
    protected $conversationService;

    public function __construct(CompanyDashboardService $dashboardService, ConversationService $conversationService)
    {
        $this->dashboardService = $dashboardService;
        $this->conversationService = $conversationService;
    }

    /**
     * Show company dashboard for an exhibitor
     */
    public function dashboard($id)
    {
        try {
            $exhibitor = Contacts::where('id', $id)->where('type', 'exhibitor')->firstOrFail();
            
            $dashboard = $this->dashboardService->getCompanyDashboard($id);
            
            return view('companies.dashboard', compact('dashboard', 'exhibitor'));
        } catch (\Exception $e) {
            return redirect()->route('contacts.index', ['type' => 'exhibitor'])
                ->with('error', 'Company not found');
        }
    }

    /**
     * Get conversation timeline for an exhibitor (AJAX)
     */
    public function getConversations(Request $request, $id)
    {
        $filters = $request->only(['outcome', 'campaign_id', 'location_id', 'date_from', 'date_to']);
        
        $conversations = $this->conversationService->getExhibitorTimeline($id, $filters);
        
        return response()->json([
            'status' => true,
            'data' => $conversations
        ]);
    }
}





