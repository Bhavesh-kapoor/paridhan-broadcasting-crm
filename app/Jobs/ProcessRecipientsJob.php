<?php

namespace App\Jobs;

use App\Models\CampaignRecipient;
use App\Models\Contacts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProcessRecipientsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaignId;
    public $recipientIds; // Array of contact IDs
    public $isUpdate;     // Delete existing recipients if true
    public $chunkSize = 500;

    public function __construct($campaignId, array $recipientIds, bool $isUpdate = false)
    {
        $this->campaignId = $campaignId;
        $this->recipientIds = $recipientIds;
        $this->isUpdate = $isUpdate;
    }

    public function handle()
    {
        // Check if campaign exists
        $campaign = \App\Models\Campaign::find($this->campaignId);
        if (!$campaign) {
            Log::channel('process_recipients')->error("Campaign not found", [
                'campaign_id' => $this->campaignId,
                'recipients_count' => count($this->recipientIds ?? [])
            ]);
            return;
        }

        if (empty($this->recipientIds)) {
            Log::channel('process_recipients')->warning("No recipients to process", ['campaign_id' => $this->campaignId]);
            return;
        }

        // Delete existing recipients if update


        $totalInserted = 0;

        // Split into chunks
        $chunks = array_chunk($this->recipientIds, $this->chunkSize);
        foreach ($chunks as $chunk) {
            $insertData = [];

            foreach ($chunk as $contactId) {
                if ($this->isUpdate) {
                    CampaignRecipient::where('campaign_id', $this->campaignId)
                        ->where('contact_id', $contactId)->delete();
                }

                $contacts = Contacts::find($contactId);

                if (!isset($contacts->id)) {

                    Log::channel('process_recipients')->warning("Contact not found, skipping", [
                        'campaign_id' => $this->campaignId,
                        'contact_id' => $contacts->id
                    ]);
                    continue;
                }

                $insertData[] = [
                    'id' => Str::ulid(),
                    'campaign_id' => $this->campaignId,
                    'contact_id' => $contacts->id,
                    'email' => $contacts->email,
                    'phone' => $contacts->phone,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($insertData)) {
                CampaignRecipient::insert($insertData);
                $totalInserted += count($insertData);
            }
        }

        // Close database connection to free up resources
        \Illuminate\Support\Facades\DB::disconnect();
    }

    public function failed(Throwable $e)
    {
        Log::channel('process_recipients')->error("ProcessRecipientsJob failed", [
            'campaign_id' => $this->campaignId,
            'error' => $e->getMessage(),
            'recipients_count' => count($this->recipientIds)
        ]);
    }
}
