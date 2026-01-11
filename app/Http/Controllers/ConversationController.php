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
    public function index(Request $request): View
    {
        $status = $request->get('status', '');
        
        // Get data for modal form
        $exhibitors = Contacts::where('type', 'exhibitor')->orderBy('name')->get();
        $visitors = Contacts::where('type', 'visitor')->orderBy('name')->get();
        $campaigns = Campaign::orderBy('name')->get();
        $locations = LocationMngt::orderBy('loc_name')->get();
        $employees = User::where('role', 'employee')->where('status', 'active')->orderBy('name')->get();
        
        return view('conversations.index', compact('status', 'exhibitors', 'visitors', 'campaigns', 'locations', 'employees'));
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
            'exhibitor_id' => 'nullable|ulid|exists:contacts,id',
            'exhibitor_name' => 'nullable|string|max:255',
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

            // Handle exhibitor_name - if provided but no exhibitor_id, create or find contact
            if (!empty($validated['exhibitor_name']) && empty($validated['exhibitor_id'])) {
                $exhibitor = Contacts::firstOrCreate(
                    ['name' => $validated['exhibitor_name'], 'type' => 'exhibitor'],
                    ['phone' => null, 'email' => null]
                );
                $validated['exhibitor_id'] = $exhibitor->id;
            }
            unset($validated['exhibitor_name']);

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
            'exhibitor_id' => 'nullable|ulid|exists:contacts,id',
            'exhibitor_name' => 'nullable|string|max:255',
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

            // Handle exhibitor_name - if provided but no exhibitor_id, create or find contact
            if (!empty($validated['exhibitor_name']) && empty($validated['exhibitor_id'])) {
                $exhibitor = Contacts::firstOrCreate(
                    ['name' => $validated['exhibitor_name'], 'type' => 'exhibitor'],
                    ['phone' => null, 'email' => null]
                );
                $validated['exhibitor_id'] = $exhibitor->id;
            }
            unset($validated['exhibitor_name']);

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

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('outcome', $request->status);
        }

        // Filter by employee if logged in as employee
        if (auth()->user()->role === 'employee') {
            $query->where('employee_id', auth()->id());
        }

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
                $userRole = auth()->user()->role;
                $routePrefix = $userRole === 'admin' ? 'conversations' : 'employee.conversations';
                $visitorId = $conversation->visitor_id ?? '';
                $exhibitorId = $conversation->exhibitor_id ?? '';
                $visitorName = ($conversation->visitor->name ?? $conversation->visitor_phone ?? 'Unknown');
                $exhibitorName = $conversation->exhibitor->name ?? 'Unknown';
                $visitorPhone = $conversation->visitor->phone ?? $conversation->visitor_phone ?? '';
                $visitorEmail = $conversation->visitor->email ?? '';
                $contactId = $visitorId ?: $exhibitorId;
                $contactName = $visitorId ? $visitorName : $exhibitorName;
                $safeName = htmlspecialchars($contactName, ENT_QUOTES, 'UTF-8');
                
                $button = '<div class="d-flex gap-1 justify-content-center">';
                if ($contactId) {
                    $button .= '<button class="btn btn-action btn-info viewCanvasBtn" data-contact-id="' . $contactId . '" data-contact-name="' . $safeName . '" data-contact-phone="' . htmlspecialchars($visitorPhone, ENT_QUOTES, 'UTF-8') . '" data-contact-email="' . htmlspecialchars($visitorEmail, ENT_QUOTES, 'UTF-8') . '" data-visitor-id="' . $visitorId . '" data-exhibitor-id="' . $exhibitorId . '" data-bs-toggle="tooltip" title="View Conversations">
                        <i class="bx bx-show"></i>
                    </button>';
                }
                $button .= '<button class="btn btn-action btn-edit editBtn" data-id="' . $id . '" data-bs-toggle="tooltip" title="Edit">
                    <i class="bx bx-edit"></i>
                </button>';
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

    /**
     * Get conversation data for editing (AJAX)
     */
    public function getConversationData($id): JsonResponse
    {
        try {
            $conversation = Conversation::with(['exhibitor', 'visitor', 'location', 'table', 'campaign', 'employee'])
                ->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $conversation->id,
                    'exhibitor_id' => $conversation->exhibitor_id,
                    'visitor_id' => $conversation->visitor_id,
                    'visitor_phone' => $conversation->visitor_phone,
                    'employee_id' => $conversation->employee_id,
                    'location_id' => $conversation->location_id,
                    'table_id' => $conversation->table_id,
                    'campaign_id' => $conversation->campaign_id,
                    'outcome' => $conversation->outcome,
                    'notes' => $conversation->notes,
                    'conversation_date' => $conversation->conversation_date ? $conversation->conversation_date->format('Y-m-d\TH:i') : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Conversation not found'
            ], 404);
        }
    }

    /**
     * Get conversations for a specific visitor or exhibitor (AJAX)
     */
    public function getConversationsForContact(Request $request): JsonResponse
    {
        $request->validate([
            'visitor_id' => 'nullable|ulid|exists:contacts,id',
            'exhibitor_id' => 'nullable|ulid|exists:contacts,id',
        ]);

        $query = Conversation::with(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'booking']);

        // Filter by visitor_id or exhibitor_id
        if ($request->has('visitor_id') && $request->visitor_id) {
            $query->where(function($q) use ($request) {
                $q->where('visitor_id', $request->visitor_id)
                  ->orWhere('exhibitor_id', $request->visitor_id);
            });
        } elseif ($request->has('exhibitor_id') && $request->exhibitor_id) {
            $query->where('exhibitor_id', $request->exhibitor_id);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Either visitor_id or exhibitor_id is required'
            ], 400);
        }

        // Filter by employee if logged in as employee
        if (auth()->user()->role === 'employee') {
            $query->where('employee_id', auth()->id());
        }

        $conversations = $query->orderBy('conversation_date', 'desc')->get();

        $conversationsData = $conversations->map(function($conv) {
            return [
                'id' => $conv->id,
                'exhibitor_name' => $conv->exhibitor->name ?? 'N/A',
                'visitor_name' => $conv->visitor->name ?? $conv->visitor_phone ?? 'N/A',
                'employee_name' => $conv->employee->name ?? 'N/A',
                'location_name' => $conv->location->loc_name ?? 'N/A',
                'table_no' => $conv->table->table_no ?? 'N/A',
                'outcome' => $conv->outcome,
                'notes' => $conv->notes,
                'conversation_date' => $conv->conversation_date->format('M d, Y H:i'),
                'has_booking' => $conv->booking ? true : false,
                'booking_id' => $conv->booking ? $conv->booking->id : null,
                'price' => $conv->booking ? number_format($conv->booking->price ?? 0, 2) : null,
                'amount_paid' => $conv->booking ? number_format($conv->booking->amount_paid ?? 0, 2) : null,
            ];
        });

        return response()->json([
            'status' => true,
            'conversations' => $conversationsData,
            'total' => $conversations->count()
        ]);
    }
}
