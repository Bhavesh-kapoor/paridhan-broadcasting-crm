<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Jobs\ProcessRecipientsJob;
use App\Models\Campaign;
use App\Services\CampaignService;
use App\Services\CampaignAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CampaignController extends Controller
{
    protected $campaignService;
    protected $analyticsService;

    public function __construct(CampaignService $campaignService, CampaignAnalyticsService $analyticsService)
    {
        $this->campaignService = $campaignService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display a listing of campaigns
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        // $campaigns = $this->campaignService->getAllCampaigns($filters);
        // 'campaigns',

        return view('campaigns.index', compact('filters'));
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
            $campaign = $this->campaignService->createCampaign($request->validated());
            return response()->json([
                'status' => true,
                'message' => 'Campaign created successfully!',
                'campaign_id' => $campaign->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create campaign: ' . $e->getMessage()
            ], 500);
        }
    }


    public function addRecipients(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'recipients'  => 'required|string',
        ]);
        try {
            dispatch(new ProcessRecipientsJob(
                $request->campaign_id,
                json_decode($request->recipients, true),
                $request->operation_type ?? false
            ));

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified campaign
     */
    public function show($id)
    {
        $campaign = $this->campaignService->getCampaignById($id);
        $analytics = $this->analyticsService->getCampaignRevenue($id);
        $revenueByExhibitor = $this->analyticsService->getRevenueByExhibitor($id);
        return view('campaigns.show', compact('campaign', 'analytics', 'revenueByExhibitor'));
    }

    /**
     * Get campaign analytics as JSON
     */
    public function analytics($id): JsonResponse
    {
        $analytics = $this->analyticsService->getCampaignRevenue($id);
        return response()->json([
            'status' => true,
            'data' => $analytics
        ]);
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


    public function progress($id)
    {
        $campaign = Campaign::with('recipients')->find($id);
        $total = $campaign->recipients->count();
        $sent = $campaign->recipients->where('status', 'sent')->count();
        $failed = $campaign->recipients->where('status', 'failed')->count();
        $pending = $total - ($sent + $failed);

        $percent = $total ? round((($sent + $failed) / $total) * 100) : 0;

        return response()->json(compact('total', 'sent', 'failed', 'pending', 'percent'));
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

    /**
     * Show conversations for a campaign
     */
    public function conversations($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $conversations = \App\Models\Conversation::where('campaign_id', $campaignId)
            ->with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'booking'])
            ->orderBy('conversation_date', 'desc')
            ->get();
        
        // Get all campaign recipients (people who received messages)
        $recipients = \App\Models\CampaignRecipient::where('campaign_id', $campaignId)
            ->with('contact')
            ->orderBy('sent_at', 'desc')
            ->get();
        
        // Get exhibitors and locations for creating conversations
        $exhibitors = \App\Models\Contacts::where('type', 'exhibitor')->orderBy('name')->get();
        $locations = \App\Models\LocationMngt::orderBy('loc_name')->get();
        $employees = \App\Models\User::where('role', 'employee')->where('status', 'active')->orderBy('name')->get();
        
        return view('campaigns.conversations', compact('campaign', 'conversations', 'recipients', 'exhibitors', 'locations', 'employees'));
    }

    /**
     * Show form to create conversation for a campaign
     */
    public function createConversation($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $exhibitors = \App\Models\Contacts::where('type', 'exhibitor')->orderBy('name')->get();
        $visitors = \App\Models\Contacts::where('type', 'visitor')->orderBy('name')->get();
        $locations = \App\Models\LocationMngt::orderBy('loc_name')->get();
        $employees = \App\Models\User::where('role', 'employee')->where('status', 'active')->orderBy('name')->get();
        
        return view('campaigns.create-conversation', compact('campaign', 'exhibitors', 'visitors', 'locations', 'employees'));
    }

    /**
     * Employee campaigns listing
     */
    public function employeeIndex()
    {
        $campaigns = Campaign::whereIn('status', ['sent', 'scheduled'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('campaigns.employee_index', compact('campaigns'));
    }

    /**
     * Show campaign recipients/visitors for employees
     */
    public function recipients($campaignId)
    {
        $campaign = Campaign::with('recipients.contact')->findOrFail($campaignId);
        $exhibitors = \App\Models\Contacts::where('type', 'exhibitor')->orderBy('name')->get();
        $locations = \App\Models\LocationMngt::orderBy('loc_name')->get();
        
        return view('campaigns.recipients', compact('campaign', 'exhibitors', 'locations'));
    }

    /**
     * Get campaign recipients list (AJAX)
     */
    public function getRecipientsList(Request $request, $campaignId): JsonResponse
    {
        $campaign = Campaign::findOrFail($campaignId);
        $recipients = $campaign->recipients()->with('contact')->get();
        
        $data = [];
        foreach ($recipients as $recipient) {
            $contact = $recipient->contact;
            if (!$contact) continue;
            
            // Check if conversation exists (with booking relationship)
            $conversation = \App\Models\Conversation::where('campaign_id', $campaignId)
                ->where('visitor_id', $contact->id)
                ->with('booking')
                ->first();
            
            // Check if booking exists
            $booking = \App\Models\Booking::where('campaign_id', $campaignId)
                ->where('visitor_id', $contact->id)
                ->first();
            
            $data[] = [
                'id' => $recipient->id,
                'contact_id' => $contact->id,
                'name' => $contact->name,
                'phone' => $contact->phone,
                'email' => $contact->email,
                'status' => $recipient->status,
                'sent_at' => $recipient->sent_at ? $recipient->sent_at->format('M d, Y H:i') : null,
                'has_conversation' => $conversation ? true : false,
                'conversation_id' => $conversation ? $conversation->id : null,
                'conversation_outcome' => $conversation ? $conversation->outcome : null,
                'has_booking' => $booking ? true : false,
                'booking_id' => $booking ? $booking->id : null,
                'booking_amount' => $booking ? number_format($booking->amount_paid ?? 0, 2) : '0.00',
                'has_invoice' => ($conversation && $conversation->booking) ? true : false,
            ];
        }
        
        return response()->json(['data' => $data]);
    }

    /**
     * Store conversation for a campaign
     */
    public function storeConversation(Request $request, $campaignId): JsonResponse
    {
        $validated = $request->validate([
            'exhibitor_id' => 'required|ulid|exists:contacts,id',
            'visitor_id' => 'nullable|ulid|exists:contacts,id',
            'visitor_phone' => 'nullable|string|max:20',
            'employee_id' => 'required|ulid|exists:users,id',
            'location_id' => 'nullable|exists:location_mngt,id',
            'table_id' => 'nullable|exists:location_mngt_table_details,id',
            'outcome' => 'required|in:busy,interested,materialised',
            'notes' => 'nullable|string|max:2000',
            'conversation_date' => 'nullable|date',
        ]);

        try {
            $validated['campaign_id'] = $campaignId;
            $validated['employee_id'] = $validated['employee_id'] ?? auth()->id();
            $validated['conversation_date'] = $validated['conversation_date'] ?? now();

            // Find campaign recipient if visitor_id is provided
            if (!empty($validated['visitor_id'])) {
                $campaignRecipient = \App\Models\CampaignRecipient::where('campaign_id', $campaignId)
                    ->where('contact_id', $validated['visitor_id'])
                    ->first();
                if ($campaignRecipient) {
                    $validated['campaign_recipient_id'] = $campaignRecipient->id;
                }
            }

            $conversationService = app(\App\Services\ConversationService::class);
            $conversation = $conversationService->create($validated);

            // If booking is enabled in request, create booking as well
            if ($request->has('enable_booking') && ($request->enable_booking == 'on' || $request->enable_booking == '1')) {
                $bookingValidated = $request->validate([
                    'booking_date' => 'required|date',
                    'booking_price' => 'required|numeric|min:0',
                    'booking_amount_paid' => 'nullable|numeric|min:0',
                    'booking_amount_status' => 'required|in:paid,partial,pending',
                ]);

                // Get table to set backward compatibility fields
                $table = null;
                $location = null;
                if ($validated['table_id']) {
                    $table = \App\Models\LocationMngtTableDetail::find($validated['table_id']);
                }
                if ($validated['location_id']) {
                    $location = \App\Models\LocationMngt::find($validated['location_id']);
                }
                
                $bookingData = [
                    'exhibitor_id' => $validated['exhibitor_id'],
                    'visitor_id' => $validated['visitor_id'] ?? null,
                    'phone' => $validated['visitor_phone'] ?? null,
                    'location_id' => $validated['location_id'],
                    'table_id' => $validated['table_id'],
                    'campaign_id' => $campaignId,
                    'employee_id' => $validated['employee_id'],
                    'price' => $bookingValidated['booking_price'],
                    'amount_paid' => $bookingValidated['booking_amount_paid'] ?? 0,
                    'amount_status' => $bookingValidated['booking_amount_status'],
                    'booking_location' => $location->loc_name ?? null,
                    'table_no' => $table->table_no ?? null,
                    'booking_date' => $bookingValidated['booking_date'],
                ];

                $booking = \App\Models\Booking::create($bookingData);
                
                // Link conversation to booking
                $conversation->update(['booking_id' => $booking->id, 'outcome' => 'materialised']);
            }

            return response()->json([
                'status' => true,
                'message' => isset($booking) ? 'Conversation and booking created successfully!' : 'Conversation added successfully!',
                'data' => $conversation->load('booking')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create conversation: ' . $e->getMessage()
            ], 500);
        }
    }
}
