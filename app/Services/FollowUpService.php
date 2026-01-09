<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FollowUp;
use App\Services\ConversationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;

use Illuminate\Support\Str;

class FollowUpService
{
    protected ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }
    public function getFollowUpData($phone)
    {
        // ===========================
        // 1. Get Contact + Latest Follow-up
        // ===========================
        $latestFollowup = FollowUp::query()
            ->leftJoin('contacts', 'contacts.phone', '=', 'follow_ups.phone')
            ->select(
                'follow_ups.*',
                'contacts.name as contact_name',
                'contacts.phone as contact_phone'
            )
            ->where('follow_ups.phone', $phone)
            ->orderByDesc('follow_ups.created_at')
            ->firstOrFail();

        // ===========================
        // 2. Get Follow-up History
        // ===========================
        $history = $this->getFollowUps($phone);

        // Return both
        return [
            'contact' => [
                'name' => $latestFollowup->contact_name,
                'phone' => $latestFollowup->contact_phone,
            ],
            'latest_followup' => $latestFollowup,
            'history' => $history
        ];
    }


    public function getAllLeadsList($filters = [])
    {
        // Step 1: Get the latest follow_up timestamp for each phone
        $latestFollowUps = DB::table('follow_ups')
            ->select('phone', DB::raw('MAX(created_at) AS max_created_at'))
            ->groupBy('phone');

        // Step 2: Main Query with LEFT JOIN SUBQUERY
        $query = DB::table('contacts')
            ->select([
                'contacts.id',
                'contacts.name',
                'contacts.phone',
                'contacts.location',
                'contacts.type',

                DB::raw("CASE
                        WHEN follow_ups.id IS NOT NULL THEN 'Done'
                        ELSE 'Pending'
                    END AS follow_status"),

                'follow_ups.status AS follow_up_status',
                'follow_ups.created_at AS follow_up_date',
            ])
            ->leftJoinSub($latestFollowUps, 'latest_followup', function ($join) {
                $join->on('contacts.phone', '=', 'latest_followup.phone');
            })
            ->leftJoin('follow_ups', function ($join) {
                $join->on('contacts.phone', '=', 'follow_ups.phone')
                    ->on('follow_ups.created_at', '=', 'latest_followup.max_created_at');
            })
            ->when(isset($filters['filter_lead_type']) && $filters['filter_lead_type'] !== '', function ($query) use ($filters) {
                return $query->where('contacts.type', $filters['filter_lead_type']);
            })
            ->orderByDesc('contacts.id');


        // Step 3: send to DataTables
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($lead) {

                $phone = $lead->phone;

                return '
                <button class="btn btn-primary btn-sm addBtn"
                        data-phone="' . $phone . '"
                        data-bs-toggle="tooltip"
                        data-bs-placement="left"
                        title="Add Follow Up">
                    <i class="bx bx-plus"></i>
                </button>
                <button class="btn btn-info btn-sm ViewBtn"
                        data-phone="' . $phone . '"  editRoute="' . route('leads.edit', $phone) . '"
                        data-bs-toggle="tooltip"
                        data-bs-placement="left"
                        title="Delete Lead">
                    <i class="bx bx-show"></i>
                </button>
            ';
            })
            ->addColumn('follow_status_badge', function ($row) {
                if ($row->follow_status == 'Done') {
                    return '<span class="badge bg-success">Done</span>';
                }
                return '<span class="badge bg-danger">Pending</span>';
            })
            ->rawColumns(['action', 'follow_status_badge'])
            ->make(true);
    }



    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            // Find campaign recipient if campaign_id and visitor_id/phone are provided
            $campaignRecipientId = null;
            if (!empty($data['campaign_id'])) {
                $visitorId = $data['visitor_id'] ?? null;
                $phone = $data['hidden_id'] ?? null;
                
                // Try to find by visitor_id first
                if ($visitorId) {
                    $campaignRecipient = \App\Models\CampaignRecipient::where('campaign_id', $data['campaign_id'])
                        ->where('contact_id', $visitorId)
                        ->first();
                    if ($campaignRecipient) {
                        $campaignRecipientId = $campaignRecipient->id;
                    }
                }
                
                // If not found, try by phone
                if (!$campaignRecipientId && $phone) {
                    $campaignRecipient = \App\Models\CampaignRecipient::where('campaign_id', $data['campaign_id'])
                        ->where('phone', $phone)
                        ->first();
                    if ($campaignRecipient) {
                        $campaignRecipientId = $campaignRecipient->id;
                        // Also set visitor_id if not set
                        if (!$data['visitor_id'] && $campaignRecipient->contact_id) {
                            $data['visitor_id'] = $campaignRecipient->contact_id;
                        }
                    }
                }
            }

            // 1️⃣ Save follow-up
            $followup = FollowUp::create([
                'phone'             => $data['hidden_id'],  // phone number
                'status'            => $data['status'],
                'comment'           => $data['comment'],
                'next_followup_date' => $data['next_followup_date'] ?? null,
                'next_followup_time' => $data['next_followup_time'] ?? null,
                'employee_id'       => auth()->id(),
                // NEW: Add context fields
                'exhibitor_id'      => $data['exhibitor_id'] ?? null,
                'visitor_id'        => $data['visitor_id'] ?? null,
                'location_id'       => $data['location_id'] ?? null,
                'table_id'          => $data['table_id'] ?? null,
                'campaign_id'       => $data['campaign_id'] ?? null,
            ]);

            // Auto-populate visitor_id from phone if not provided
            if (empty($followup->visitor_id) && !empty($followup->phone)) {
                $contact = \App\Models\Contacts::where('phone', $followup->phone)->first();
                if ($contact) {
                    $followup->visitor_id = $contact->id;
                    $followup->save();
                }
            }

            // Sync location_id with booking_location if booking_location is provided (backward compat)
            if (empty($followup->location_id) && !empty($data['booking_location'])) {
                $location = \App\Models\LocationMngt::where('loc_name', $data['booking_location'])->first();
                if ($location) {
                    $followup->location_id = $location->id;
                    $followup->save();
                }
            }

            // Sync table_id with table_no if table_no is provided (backward compat)
            if (empty($followup->table_id) && !empty($data['table_no'])) {
                // Try to find table by ID or table_no
                $table = \App\Models\LocationMngtTableDetail::where('id', $data['table_no'])
                    ->orWhere('table_no', $data['table_no'])
                    ->first();
                if ($table) {
                    $followup->table_id = $table->id;
                    $followup->save();
                }
            }

            $booking = null;

            // 2️⃣ If Materialised → store in bookings table
            if ($data['status'] === 'materialised') {
                $booking = Booking::create([
                    'id'               => (string) Str::ulid(),
                    'phone'            => $data['hidden_id'], // hidden_id = phone
                    'booking_date'     => $data['booking_date'],
                    'booking_location' => $data['booking_location'] ?? null, // Backward compat
                    'table_no'         => $data['table_no'] ?? null, // Backward compat
                    'price'            => $data['price'],
                    'amount_status'    => $data['amount_status'],
                    'amount_paid'      => $data['amount_paid'],
                    'employee_id'      => auth()->id(),
                    // NEW: Add context fields
                    'exhibitor_id'     => $data['exhibitor_id'] ?? null,
                    'visitor_id'       => $data['visitor_id'] ?? null,
                    'location_id'      => $data['location_id'] ?? null,
                    'table_id'         => $data['table_id'] ?? null,
                    'campaign_id'      => $data['campaign_id'] ?? null,
                ]);

                // Auto-populate visitor_id from phone if not provided
                if (empty($booking->visitor_id) && !empty($booking->phone)) {
                    $contact = \App\Models\Contacts::where('phone', $booking->phone)->first();
                    if ($contact) {
                        $booking->visitor_id = $contact->id;
                        $booking->save();
                    }
                }

                // Sync location_id with booking_location if provided (backward compat)
                if (empty($booking->location_id) && !empty($data['booking_location'])) {
                    // Try to find by ID first, then by name
                    $location = null;
                    if (is_numeric($data['booking_location'])) {
                        $location = \App\Models\LocationMngt::find($data['booking_location']);
                    } else {
                        $location = \App\Models\LocationMngt::where('loc_name', $data['booking_location'])->first();
                    }
                    if ($location) {
                        $booking->location_id = $location->id;
                        $booking->save();
                    }
                }

                // Sync table_id with table_no if provided (backward compat)
                if (empty($booking->table_id) && !empty($data['table_no'])) {
                    // Try to find table by ID first (if numeric), then by table_no
                    if (is_numeric($data['table_no'])) {
                        $table = \App\Models\LocationMngtTableDetail::find($data['table_no']);
                    } else {
                        $table = \App\Models\LocationMngtTableDetail::where('table_no', $data['table_no'])->first();
                    }
                    if ($table) {
                        $booking->table_id = $table->id;
                        $booking->save();
                    }
                }

                // Sync booking_location and table_no with location_id and table_id (for backward compat display)
                if (!empty($booking->location_id)) {
                    $location = \App\Models\LocationMngt::find($booking->location_id);
                    if ($location && empty($booking->booking_location)) {
                        $booking->booking_location = $location->loc_name;
                        $booking->save();
                    }
                }

                if (!empty($booking->table_id)) {
                    $table = \App\Models\LocationMngtTableDetail::find($booking->table_id);
                    if ($table && empty($booking->table_no)) {
                        $booking->table_no = $table->table_no ?? $table->id;
                        $booking->save();
                    }
                }

                // Create conversation from booking
                $this->conversationService->createFromBooking($booking);
            } else {
                // Create conversation from follow-up (will automatically link campaign_recipient)
                $this->conversationService->createFromFollowUp($followup);
            }

            DB::commit();
            return $followup->load(['exhibitor', 'visitor', 'location', 'table', 'campaign', 'conversation']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function getFollowUps($phone)
    {
        $data = FollowUp::where('follow_ups.phone', $phone)
            ->leftJoin('users', 'follow_ups.employee_id', '=', 'users.id')
            ->select(
                'follow_ups.status',
                'follow_ups.comment',
                'follow_ups.next_followup_date',
                'follow_ups.next_followup_time',
                'follow_ups.created_at',
                'users.name as users_name'
            )
            ->orderBy('follow_ups.created_at', 'DESC')
            ->get()
            ->map(function ($item) {

                $date = Carbon::parse($item->created_at);

                $item->formatted_date = $date->format('d-m-y');  // dd-mm-yy
                $item->formatted_time = $date->format('h:i A');  // hh:mm AM/PM

                return $item;
            });

        return $data;
    }
}
