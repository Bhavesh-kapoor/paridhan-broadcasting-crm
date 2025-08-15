<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Contacts;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    /**
     * Get all campaigns with pagination
     */
    public function getAllCampaigns($filters = [])
    {
        $query = Campaign::with('recipients');

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * Get campaign by ID
     */
    public function getCampaignById($id)
    {
        return Campaign::with('recipients')->findOrFail($id);
    }

    /**
     * Create a new campaign
     */
    public function createCampaign($data)
    {
        DB::beginTransaction();
        
        try {
            $campaign = Campaign::create([
                'name' => $data['name'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'type' => $data['type'],
                'status' => 'draft',
                'scheduled_at' => $data['scheduled_at'] ?? null,
            ]);

            // Add recipients if provided
            if (!empty($data['recipients'])) {
                $this->addRecipientsToCampaign($campaign->id, $data['recipients']);
            }

            DB::commit();
            return $campaign;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing campaign
     */
    public function updateCampaign($id, $data)
    {
        DB::beginTransaction();
        
        try {
            $campaign = $this->getCampaignById($id);
            
            $updateData = [
                'name' => $data['name'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'type' => $data['type'],
                'scheduled_at' => $data['scheduled_at'] ?? null,
            ];

            $campaign->update($updateData);

            // Update recipients if provided
            if (isset($data['recipients'])) {
                $this->updateRecipientsForCampaign($campaign->id, $data['recipients']);
            }

            DB::commit();
            return $campaign;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a campaign
     */
    public function deleteCampaign($id)
    {
        DB::beginTransaction();
        
        try {
            $campaign = $this->getCampaignById($id);
            
            // Delete recipients first
            CampaignRecipient::where('campaign_id', $id)->delete();
            
            $campaign->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Send campaign
     */
    public function sendCampaign($id)
    {
        DB::beginTransaction();
        
        try {
            $campaign = $this->getCampaignById($id);
            
            if ($campaign->status !== 'draft') {
                throw new \Exception('Campaign can only be sent from draft status.');
            }

            $campaign->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Here you would integrate with your messaging service (Wati, etc.)
            // For now, we'll just mark it as sent
            
            DB::commit();
            return $campaign;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get contacts for campaign selection
     */
    public function getContactsForCampaign($type = null)
    {
        $query = Contacts::query();
        
        if ($type) {
            $query->where('type', $type);
        }

        return $query->select('id', 'name', 'email', 'phone', 'type', 'location')
                    ->orderBy('name')
                    ->get();
    }

    /**
     * Add recipients to campaign
     */
    private function addRecipientsToCampaign($campaignId, $recipientIds)
    {
        $recipients = [];
        foreach ($recipientIds as $contactId) {
            $contact = Contacts::find($contactId);
            if ($contact) {
                $recipients[] = [
                    'campaign_id' => $campaignId,
                    'contact_id' => $contactId,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($recipients)) {
            CampaignRecipient::insert($recipients);
        }
    }

    /**
     * Update recipients for campaign
     */
    private function updateRecipientsForCampaign($campaignId, $recipientIds)
    {
        // Remove existing recipients
        CampaignRecipient::where('campaign_id', $campaignId)->delete();
        
        // Add new recipients
        $this->addRecipientsToCampaign($campaignId, $recipientIds);
    }

    /**
     * Get campaign statistics
     */
    public function getCampaignStats()
    {
        return [
            'total' => Campaign::count(),
            'draft' => Campaign::where('status', 'draft')->count(),
            'sent' => Campaign::where('status', 'sent')->count(),
            'scheduled' => Campaign::where('status', 'scheduled')->count(),
        ];
    }
}
