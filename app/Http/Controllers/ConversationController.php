<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Contacts;
use App\Models\Campaign;
use App\Models\LocationMngt;
use App\Models\LocationMngtTableDetail;
use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ConversationController extends Controller
{
    protected $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    /**
     * Display a listing of conversations
     */
    public function index(): View
    {
        return view('conversations.index');
    }

    /**
     * Show the form for creating a new conversation
     */
    public function create(): View
    {
        $exhibitors = Contacts::where('type', 'exhibitor')->orderBy('name')->get();
        $visitors = Contacts::where('type', 'visitor')->orderBy('name')->get();
        $campaigns = Campaign::orderBy('name')->get();
        $locations = LocationMngt::orderBy('loc_name')->get();
        $employees = User::where('role', 'employee')->where('status', 'active')->orderBy('name')->get();

        return view('conversations.create', compact('exhibitors', 'visitors', 'campaigns', 'locations', 'employees'));
    }

    /**
     * Store a newly created conversation
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exhibitor_id' => 'required|ulid|exists:contacts,id',
            'visitor_id' => 'nullable|ulid|exists:contacts,id',
            'visitor_phone' => 'nullable|string|max:20',
            'employee_id' => 'required|ulid|exists:users,id',
            'location_id' => 'nullable|exists:location_mngt,id',
            'table_id' => 'nullable|exists:location_mngt_table_details,id',
            'campaign_id' => 'nullable|ulid|exists:campaigns,id',
            'outcome' => 'required|in:busy,interested,materialised',
            'notes' => 'nullable|string|max:2000',
            'conversation_date' => 'nullable|date',
        ]);

        try {
            $validated['employee_id'] = $validated['employee_id'] ?? auth()->id();
            $validated['conversation_date'] = $validated['conversation_date'] ?? now();

            // Find campaign recipient if campaign_id and visitor_id are provided
            if (!empty($validated['campaign_id']) && !empty($validated['visitor_id'])) {
                $campaignRecipient = \App\Models\CampaignRecipient::where('campaign_id', $validated['campaign_id'])
                    ->where('contact_id', $validated['visitor_id'])
                    ->first();
                if ($campaignRecipient) {
                    $validated['campaign_recipient_id'] = $campaignRecipient->id;
                }
            }

            $conversation = $this->conversationService->create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Conversation created successfully!',
                'data' => $conversation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified conversation
     */
    public function show($id): View
    {
        $conversation = Conversation::with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'campaignRecipient', 'followUp', 'booking'])
            ->findOrFail($id);
        return view('conversations.show', compact('conversation'));
    }

    /**
     * Show the form for editing the specified conversation
     */
    public function edit($id): View
    {
        $conversation = Conversation::findOrFail($id);
        $exhibitors = Contacts::where('type', 'exhibitor')->orderBy('name')->get();
        $visitors = Contacts::where('type', 'visitor')->orderBy('name')->get();
        $campaigns = Campaign::orderBy('name')->get();
        $locations = LocationMngt::orderBy('loc_name')->get();
        $employees = User::where('role', 'employee')->where('status', 'active')->orderBy('name')->get();
        $tables = $conversation->location_id 
            ? LocationMngtTableDetail::where('location_mngt_id', $conversation->location_id)->get()
            : collect();

        return view('conversations.edit', compact('conversation', 'exhibitors', 'visitors', 'campaigns', 'locations', 'employees', 'tables'));
    }

    /**
     * Update the specified conversation
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'exhibitor_id' => 'required|ulid|exists:contacts,id',
            'visitor_id' => 'nullable|ulid|exists:contacts,id',
            'visitor_phone' => 'nullable|string|max:20',
            'employee_id' => 'required|ulid|exists:users,id',
            'location_id' => 'nullable|exists:location_mngt,id',
            'table_id' => 'nullable|exists:location_mngt_table_details,id',
            'campaign_id' => 'nullable|ulid|exists:campaigns,id',
            'outcome' => 'required|in:busy,interested,materialised',
            'notes' => 'nullable|string|max:2000',
            'conversation_date' => 'nullable|date',
        ]);

        try {
            $conversation = Conversation::findOrFail($id);

            // Find campaign recipient if campaign_id and visitor_id are provided
            if (!empty($validated['campaign_id']) && !empty($validated['visitor_id'])) {
                $campaignRecipient = \App\Models\CampaignRecipient::where('campaign_id', $validated['campaign_id'])
                    ->where('contact_id', $validated['visitor_id'])
                    ->first();
                if ($campaignRecipient) {
                    $validated['campaign_recipient_id'] = $campaignRecipient->id;
                }
            }

            $conversation->update($validated);
            $conversation->load(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'campaignRecipient']);

            return response()->json([
                'status' => true,
                'message' => 'Conversation updated successfully!',
                'data' => $conversation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified conversation
     */
    public function destroy($id): JsonResponse
    {
        try {
            $conversation = Conversation::findOrFail($id);
            $conversation->delete();

            return response()->json([
                'status' => true,
                'message' => 'Conversation deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all conversations for DataTables
     */
    public function getAllConversationsList(Request $request)
    {
        $query = Conversation::with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('exhibitor_name', function ($conversation) {
                return $conversation->exhibitor->name ?? '-';
            })
            ->addColumn('visitor_name', function ($conversation) {
                return $conversation->visitor->name ?? ($conversation->visitor_phone ?? '-');
            })
            ->addColumn('employee_name', function ($conversation) {
                return $conversation->employee->name ?? '-';
            })
            ->addColumn('location_name', function ($conversation) {
                return $conversation->location->loc_name ?? '-';
            })
            ->addColumn('table_name', function ($conversation) {
                return $conversation->table->table_no ?? '-';
            })
            ->addColumn('campaign_name', function ($conversation) {
                return $conversation->campaign->name ?? '-';
            })
            ->addColumn('outcome_badge', function ($conversation) {
                $badgeClass = match($conversation->outcome) {
                    'materialised' => 'bg-success',
                    'busy' => 'bg-warning',
                    'interested' => 'bg-info',
                    default => 'bg-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($conversation->outcome) . '</span>';
            })
            ->addColumn('action', function ($conversation) {
                $id = $conversation->id;
                $button = '<div class="d-flex gap-1 justify-content-center">';
                $button .= '<a href="' . route('conversations.show', $id) . '" class="btn btn-action btn-view" data-bs-toggle="tooltip" title="View">
                    <i class="bx bx-show"></i>
                </a>';
                $button .= '<a href="' . route('conversations.edit', $id) . '" class="btn btn-action btn-edit" data-bs-toggle="tooltip" title="Edit">
                    <i class="bx bx-edit"></i>
                </a>';
                $button .= '<button class="btn btn-action btn-delete deleteBtn" data-id="' . $id . '" data-bs-toggle="tooltip" title="Delete">
                    <i class="bx bx-trash"></i>
                </button>';
                $button .= '</div>';
                return $button;
            })
            ->editColumn('conversation_date', function ($conversation) {
                return $conversation->conversation_date ? $conversation->conversation_date->format('M d, Y H:i') : '-';
            })
            ->rawColumns(['outcome_badge', 'action'])
            ->make(true);
    }

    /**
     * Get tables by location (AJAX)
     */
    public function getTables($locationId): JsonResponse
    {
        $tables = LocationMngtTableDetail::where('location_mngt_id', $locationId)->get();
        return response()->json($tables);
    }
}
