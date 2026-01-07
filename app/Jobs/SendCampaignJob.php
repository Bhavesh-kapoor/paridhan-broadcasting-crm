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

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

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
            Log::channel('campaign_progress')->error('Campaign not found', [
                'campaign_id' => $this->campaignId,
            ]);
            return;
        }

        // Only pending recipients
        $recipients = $campaign->recipients()
            ->where('status', 'pending')
            ->get();

        $total = $recipients->count();
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $index => $recipient) {
            try {

                /* ---------------- BUILD COMPONENTS ---------------- */

                $components = [];

                // HEADER IMAGE (optional) - must be public HTTPS URL
                // Default test image for local development if no image provided
                $defaultTestImage = 'https://d8iqbmvu05s9c.cloudfront.net/ajprhqgqg1otf7d5sm7u3brf27gv';
                
                if (!empty($campaign->image)) {
                    // Convert local path to full HTTPS URL if needed
                    $imageUrl = $campaign->image;

                    // If it's a local path, convert to full URL
                    if (!str_starts_with($imageUrl, 'http')) {
                        $imageUrl = url('uploads/campaign_images/' . $campaign->image);
                    }

                    // Try to convert HTTP to HTTPS if possible
                    if (str_starts_with($imageUrl, 'http://') && !str_starts_with($imageUrl, 'https://')) {
                        $imageUrl = str_replace('http://', 'https://', $imageUrl);
                        Log::channel('campaign_progress')->info('Converted HTTP to HTTPS for image URL', [
                            'campaign_id' => $campaign->id,
                            'image_url' => $imageUrl,
                        ]);
                    }

                    // Validate HTTPS (Meta requires HTTPS)
                    if (!str_starts_with($imageUrl, 'https://')) {
                        // If not HTTPS, use default test image in local development
                        if (app()->environment('local')) {
                            $imageUrl = $defaultTestImage;
                            Log::channel('campaign_progress')->info('Using default test image for local development', [
                                'campaign_id' => $campaign->id,
                                'original_url' => $campaign->image,
                                'using_url' => $imageUrl,
                            ]);
                        } else {
                            Log::channel('campaign_progress')->warning('Image URL is not HTTPS, skipping header. Template may require image header!', [
                                'campaign_id' => $campaign->id,
                                'image_url' => $imageUrl,
                                'warning' => 'If template requires image header, this will cause error 132012. Use HTTPS image URL or template without image requirement.',
                            ]);
                            // Don't add header if not HTTPS in production
                            $imageUrl = null;
                        }
                    }
                } else {
                    // No image provided - use default test image in local development
                    if (app()->environment('local')) {
                        $imageUrl = $defaultTestImage;
                        Log::channel('campaign_progress')->info('No image provided, using default test image for local development', [
                            'campaign_id' => $campaign->id,
                            'image_url' => $imageUrl,
                        ]);
                    } else {
                        Log::channel('campaign_progress')->warning('Campaign has no image. If template requires image header, this may cause error 132012.', [
                            'campaign_id' => $campaign->id,
                        ]);
                    }
                }

                // Add image header if we have a valid HTTPS URL
                if (!empty($imageUrl) && str_starts_with($imageUrl, 'https://')) {
                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'link' => $imageUrl,
                                ],
                            ],
                        ],
                    ];
                }

                // BODY VARIABLES ({{1}}, {{2}})
                $components[] = [
                    'type' => 'body',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => $campaign->name,    // {{1}}
                        ],
                        [
                            'type' => 'text',
                            'text' => $campaign->message, // {{2}}
                        ],
                    ],
                ];

                /* ---------------- PAYLOAD ---------------- */

                // Format phone number: Ensure it has country code (India = 91)
                $phoneNumber = $recipient->phone;
                
                // Remove any spaces, dashes, or special characters
                $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
                
                // If phone doesn't start with country code, add India code (91)
                if (!str_starts_with($phoneNumber, '91') && strlen($phoneNumber) == 10) {
                    $phoneNumber = '91' . $phoneNumber;
                    Log::channel('campaign_progress')->info('Added country code to phone number', [
                        'campaign_id' => $campaign->id,
                        'recipient_id' => $recipient->id,
                        'original_phone' => $recipient->phone,
                        'formatted_phone' => $phoneNumber,
                    ]);
                }

                $payload = [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $phoneNumber,
                    'type' => 'template',
                    'template' => [
                        'name' => config('services.whatsapp.template_name', 'campaign_message_v2'),
                        'language' => [
                            'code' => 'en',
                        ],
                        'components' => $components,
                    ],
                    'biz_opaque_callback_data' => 'campaign_' . $campaign->id,
                ];

                /* ---------------- SEND REQUEST ---------------- */

                // Get authentication token (supports both Bearer and X-API-KEY per documentation)
                $apiKey = config('services.whatsapp.api_key');
                $bearerToken = config('services.whatsapp.bearer_token');

                if (empty($apiKey) && empty($bearerToken)) {
                    throw new \Exception('WhatsApp API Key is not configured. Please set WHATSAPP_API_KEY in .env file.');
                }

                // Use Bearer token if available, otherwise use X-API-KEY
                // Documentation supports both: 'authorization: Bearer <token>' OR 'X-API-KEY: <token>'
                $headers = [
                    'Content-Type' => 'application/json',
                ];

                if (!empty($bearerToken)) {
                    $headers['Authorization'] = 'Bearer ' . $bearerToken;
                } else {
                    $headers['X-API-KEY'] = $apiKey;
                }

                $response = \Illuminate\Support\Facades\Http::withHeaders($headers)->post(
                    config('services.whatsapp.endpoint'),
                    $payload
                );

                if ($response->successful()) {
                    $status = 'sent';
                    $sent++;

                    $responseBody = $response->json();
                    $messageId = $responseBody['messages'][0]['id'] ?? 'unknown';
                    $waId = $responseBody['contacts'][0]['wa_id'] ?? 'unknown';

                    Log::channel('campaign_progress')->info('Message sent successfully', [
                        'campaign_id' => $campaign->id,
                        'recipient_id' => $recipient->id,
                        'phone' => $phoneNumber,
                        'wa_id' => $waId,
                        'message_id' => $messageId,
                        'api_response' => $responseBody,
                    ]);
                } else {
                    $status = 'failed';
                    $failed++;

                    $responseBody = $response->json();
                    $errorCode = $responseBody['error']['code'] ?? 'unknown';
                    $errorMessage = $responseBody['error']['message'] ?? $response->body();

                    $errorContext = [
                        'campaign_id' => $campaign->id,
                        'recipient_id' => $recipient->id,
                        'phone' => $phoneNumber,
                        'original_phone' => $recipient->phone,
                        'http_status' => $response->status(),
                        'error_code' => $errorCode,
                        'error_message' => $errorMessage,
                        'full_response' => $response->body(),
                        'request_payload' => $payload,
                    ];

                    // Log to campaign progress
                    Log::channel('campaign_progress')->error('WhatsApp API error', $errorContext);

                    // Also log to dedicated API errors channel
                    Log::channel('whatsapp_api_errors')->error('WhatsApp API error', $errorContext);

                    // Handle specific error codes
                    if ($errorCode == 131049) {
                        // Meta Policy Violation
                        Log::channel('campaign_progress')->critical('Meta Policy Violation - Healthy Ecosystem', [
                            'campaign_id' => $campaign->id,
                            'recipient_id' => $recipient->id,
                            'message' => 'Message blocked by Meta to maintain healthy ecosystem engagement. Check account quality rating.',
                        ]);

                        Log::channel('whatsapp_api_errors')->critical('Meta Policy Violation - Healthy Ecosystem', [
                            'campaign_id' => $campaign->id,
                            'recipient_id' => $recipient->id,
                            'message' => 'Message blocked by Meta to maintain healthy ecosystem engagement. Check account quality rating.',
                        ]);
                    } elseif ($errorCode == 132012) {
                        // Template format mismatch - usually means template requires image header but we didn't send it
                        $hasImageHeader = !empty($campaign->image) && str_starts_with($campaign->image, 'https://');
                        
                        Log::channel('campaign_progress')->critical('Template Format Mismatch - Image Header Required', [
                            'campaign_id' => $campaign->id,
                            'recipient_id' => $recipient->id,
                            'error_code' => 132012,
                            'message' => 'Template requires an IMAGE header, but campaign image is missing or not HTTPS.',
                            'campaign_has_image' => !empty($campaign->image),
                            'image_is_https' => $hasImageHeader,
                            'solution' => 'Either: 1) Add a valid HTTPS image to the campaign, OR 2) Use a template without image header requirement.',
                        ]);

                        Log::channel('whatsapp_api_errors')->critical('Template Format Mismatch - Image Header Required', [
                            'campaign_id' => $campaign->id,
                            'recipient_id' => $recipient->id,
                            'error_code' => 132012,
                            'message' => 'Template requires an IMAGE header, but campaign image is missing or not HTTPS.',
                        ]);
                    }
                }

            } catch (\Throwable $e) {
                $status = 'failed';
                $failed++;

                Log::channel('campaign_progress')->error('WhatsApp send exception', [
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Update recipient status
            $recipient->update([
                'status' => $status,
                'sent_at' => $status === 'sent' ? now() : null,
            ]);

            // Rate limiting: Add delay between messages to avoid Meta spam detection
            // Meta allows up to 80 messages/second, but we'll be conservative
            if ($index < $total - 1) { // Don't sleep after last message
                usleep(250000); // 250ms delay = max 4 messages/second
            }
        }

        $pending = $total - ($sent + $failed);

        Log::channel('campaign_progress')->info('Campaign sending completed', [
            'campaign_id' => $campaign->id,
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
        ]);

        // Close database connection to free up resources
        \Illuminate\Support\Facades\DB::disconnect();
    }
}
