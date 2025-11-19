<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use Illuminate\Support\Facades\Log;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignId;

    /**
     * Create a new job instance.
     *
     * @param string $campaignId
     */
    public function __construct($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $campaign = Campaign::find($this->campaignId);

        if (!$campaign) {
            Log::channel('campaign_progress')->warning("Campaign not found", [
                'campaign_id' => $this->campaignId
            ]);
            return;
        }

        // Fetch all pending recipients
        $recipients = $campaign->recipients()->where('status', 'pending')->get();
        $total = $recipients->count();
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $recipient) {
            try {
                // Example: Send via Wati API
                // $response = Http::post('https://wati.io/api/v1/sendMessage', [
                //     'phone' => $recipient->phone,
                //     'message' => $campaign->message,
                //     'type' => 'text',
                // ]);

                $status = 'sent'; // or $response->successful() ? 'sent' : 'failed'

            } catch (\Exception $e) {
                $status = 'failed';

                // Log failure to campaign_progress.log
                Log::channel('campaign_progress')->error("Failed to send message", [
                    'campaign_id' => $this->campaignId,
                    'recipient_id' => $recipient->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Update recipient status
            $recipient->update([
                'status' => $status,
                'sent_at' => $status === 'sent' ? now() : null,
            ]);

            // Update counts
            if ($status === 'sent') $sent++;
            if ($status === 'failed') $failed++;
            $pending = $total - ($sent + $failed);



            // Log progress to campaign_progress.log
            // Log::channel('campaign_progress')->info("Campaign progress", [
            //     'campaign_id' => $this->campaignId,
            //     'recipient_id' => $recipient->id,
            //     'status' => $status,
            //     'sent' => $sent,
            //     'failed' => $failed,
            //     'pending' => $pending
            // ]);
        }

        // Final completion log
        Log::channel('campaign_progress')->info("Campaign sending completed", [
            'campaign_id' => $this->campaignId,
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending
        ]);
    }
}
