<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\FollowUp;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    /**
     * Create a conversation record
     */
    public function create(array $data): Conversation
    {
        DB::beginTransaction();
        try {
            $conversation = Conversation::create([
                'exhibitor_id' => $data['exhibitor_id'],
                'visitor_id' => $data['visitor_id'] ?? null,
                'visitor_phone' => $data['visitor_phone'] ?? null,
                'employee_id' => $data['employee_id'],
                'location_id' => $data['location_id'] ?? null,
                'table_id' => $data['table_id'] ?? null,
                'campaign_id' => $data['campaign_id'] ?? null,
                'campaign_recipient_id' => $data['campaign_recipient_id'] ?? null,
                'outcome' => $data['outcome'],
                'notes' => $data['notes'] ?? null,
                'conversation_date' => $data['conversation_date'] ?? now(),
                'follow_up_id' => $data['follow_up_id'] ?? null,
                'booking_id' => $data['booking_id'] ?? null,
            ]);

            // Link back to follow-up if provided
            if (isset($data['follow_up_id'])) {
                FollowUp::where('id', $data['follow_up_id'])
                    ->update(['conversation_id' => $conversation->id]);
            }

            // Link back to booking if provided
            if (isset($data['booking_id'])) {
                Booking::where('id', $data['booking_id'])
                    ->update(['conversation_id' => $conversation->id]);
            }

            DB::commit();
            return $conversation->load(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign', 'campaignRecipient']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get conversation timeline for an exhibitor
     */
    public function getExhibitorTimeline($exhibitorId, array $filters = [])
    {
        $query = Conversation::forExhibitor($exhibitorId)
            ->with(['visitor', 'employee', 'location', 'table', 'campaign', 'campaignRecipient', 'followUp', 'booking'])
            ->orderBy('conversation_date', 'desc');

        if (isset($filters['outcome'])) {
            $query->byOutcome($filters['outcome']);
        }

        if (isset($filters['campaign_id'])) {
            $query->fromCampaign($filters['campaign_id']);
        }

        if (isset($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('conversation_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('conversation_date', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    /**
     * Get recent conversations for an exhibitor
     */
    public function getRecentConversations($exhibitorId, int $limit = 10)
    {
        return Conversation::forExhibitor($exhibitorId)
            ->recent($limit)
            ->with(['visitor', 'employee', 'location', 'table', 'campaign', 'campaignRecipient'])
            ->get();
    }

    /**
     * Create conversation from follow-up
     */
    public function createFromFollowUp(FollowUp $followUp, array $additionalData = []): Conversation
    {
        // Map status to outcome
        $outcomeMap = [
            'busy' => 'busy',
            'interested' => 'interested',
            'materialised' => 'materialised',
        ];
        
        // Find campaign recipient if campaign_id and visitor_id are available
        $campaignRecipientId = null;
        if ($followUp->campaign_id && $followUp->visitor_id) {
            $campaignRecipient = \App\Models\CampaignRecipient::where('campaign_id', $followUp->campaign_id)
                ->where('contact_id', $followUp->visitor_id)
                ->first();
            if ($campaignRecipient) {
                $campaignRecipientId = $campaignRecipient->id;
            }
        }
        
        return $this->create(array_merge([
            'exhibitor_id' => $followUp->exhibitor_id,
            'visitor_id' => $followUp->visitor_id,
            'visitor_phone' => $followUp->phone,
            'employee_id' => $followUp->employee_id,
            'location_id' => $followUp->location_id,
            'table_id' => $followUp->table_id,
            'campaign_id' => $followUp->campaign_id,
            'campaign_recipient_id' => $campaignRecipientId,
            'outcome' => $outcomeMap[$followUp->status] ?? $followUp->status,
            'notes' => $followUp->comment,
            'conversation_date' => $followUp->created_at,
            'follow_up_id' => $followUp->id,
        ], $additionalData));
    }

    /**
     * Create conversation from booking
     */
    public function createFromBooking(Booking $booking, array $additionalData = []): Conversation
    {
        $tableName = $booking->table ? $booking->table->table_name : ($booking->table_no ?? 'N/A');
        
        // Find campaign recipient if campaign_id and visitor_id are available
        $campaignRecipientId = null;
        if ($booking->campaign_id && $booking->visitor_id) {
            $campaignRecipient = \App\Models\CampaignRecipient::where('campaign_id', $booking->campaign_id)
                ->where('contact_id', $booking->visitor_id)
                ->first();
            if ($campaignRecipient) {
                $campaignRecipientId = $campaignRecipient->id;
            }
        }
        
        return $this->create(array_merge([
            'exhibitor_id' => $booking->exhibitor_id,
            'visitor_id' => $booking->visitor_id,
            'visitor_phone' => $booking->phone,
            'employee_id' => $booking->employee_id,
            'location_id' => $booking->location_id,
            'table_id' => $booking->table_id,
            'campaign_id' => $booking->campaign_id,
            'campaign_recipient_id' => $campaignRecipientId,
            'outcome' => 'materialised',
            'notes' => "Booking created - Table: {$tableName}, Amount: ₹" . number_format($booking->price, 2),
            'conversation_date' => $booking->booking_date ?? $booking->created_at,
            'booking_id' => $booking->id,
        ], $additionalData));
    }
}

