<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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
     * Fetch templates from Meta API (Fallback logic)
     */
    private function fetchTemplatesFromAPI()
    {
        try {
            // Get configuration
            $apiKey = config('services.whatsapp.api_key');
            $bearerToken = config('services.whatsapp.bearer_token');
            $wabaId = config('services.whatsapp.waba_id');
            $apiVersion = config('services.whatsapp.api_version', 'v23.0');
            $baseUrl = config('services.whatsapp.base_url');
            $templateEndpoint = config('services.whatsapp.template_endpoint');

            if (empty($apiKey) && empty($bearerToken)) {
                Log::channel('campaign_progress')->error('WhatsApp API Key is not configured.');
                return [];
            }

            if (empty($wabaId)) {
                Log::channel('campaign_progress')->error('WABA ID is not configured.');
                return [];
            }
            
            // Build endpoint URL
            if (!empty($templateEndpoint)) {
                $endpoint = str_replace(
                    ['{version}', '{wabaId}', '{apiVersion}', '{waba_id}'],
                    [$apiVersion, $wabaId, $apiVersion, $wabaId],
                    $templateEndpoint
                );
            } else {
                if (empty($baseUrl)) {
                    Log::channel('campaign_progress')->error('WhatsApp Base URL is not configured.');
                    return [];
                }
                $baseUrl = rtrim($baseUrl, '/');
                $endpoint = "{$baseUrl}/{$apiVersion}/{$wabaId}/message_templates";
            }
            
            // Set headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            if (!empty($bearerToken)) {
                $headers['Authorization'] = 'Bearer ' . $bearerToken;
            } elseif (!empty($apiKey)) {
                $headers['X-API-KEY'] = $apiKey;
            }

            // Fetch templates
            $response = Http::withHeaders($headers)
                ->timeout(60)
                ->get($endpoint, [
                    'limit' => 1000,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $templates = [];
                if (isset($data['data']) && is_array($data['data'])) {
                    $templates = $data['data'];
                } elseif (is_array($data)) {
                    if (isset($data[0]) && is_array($data[0])) {
                        $templates = $data;
                    } else {
                        $templates = [$data];
                    }
                }
                
                return $templates;
            } else {
                Log::channel('campaign_progress')->error('Failed to fetch templates from API: ' . $response->status() . ' - ' . $response->body());
                return ['error' => 'API Error: ' . $response->status()];
            }
        } catch (\Exception $e) {
            Log::channel('campaign_progress')->error('Exception fetching templates: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
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

                /* ---------------- FETCH TEMPLATE DATA ---------------- */
                
                // Get template name from campaign (required for WhatsApp campaigns)
                $templateName = $campaign->template_name ?? null;
                
                if (empty($templateName)) {
                    throw new \Exception('Template name is required for WhatsApp campaigns. Please select a template when creating the campaign.');
                }

                // Fetch template from cache or API
                $templates = Cache::get('whatsapp_templates', []);
                $template = collect($templates)->firstWhere('name', $templateName);
                
                if (!$template) {
                    // Try to fetch from API if not in cache
                    Log::channel('campaign_progress')->warning('Template not found in cache, attempting to fetch from API', [
                        'campaign_id' => $campaign->id,
                        'template_name' => $templateName,
                    ]);
                    
                    // Fetch from API
                    $fetchedTemplates = $this->fetchTemplatesFromAPI();
                    
                    if (isset($fetchedTemplates['error'])) {
                         throw new \Exception("Template '{$templateName}' not found and API fetch failed: " . $fetchedTemplates['error']);
                    }

                    // Update cache
                    Cache::put('whatsapp_templates', $fetchedTemplates, 300);
                    
                    // Find template in fetched results
                    $template = collect($fetchedTemplates)->firstWhere('name', $templateName);

                    if (!$template) {
                        throw new \Exception("Template '{$templateName}' not found even after API refresh. Please ensure the template exists and is approved.");
                    }
                }

                // Validate template is approved
                if (strtoupper($template['status'] ?? '') !== 'APPROVED') {
                    throw new \Exception("Template '{$templateName}' is not approved. Status: " . ($template['status'] ?? 'UNKNOWN'));
                }

                Log::channel('campaign_progress')->info('Template found for campaign', [
                    'campaign_id' => $campaign->id,
                    'template_name' => $templateName,
                    'template_status' => $template['status'] ?? 'UNKNOWN',
                    'template_language' => $template['language'] ?? 'en',
                    'template_components' => json_encode($template['components'] ?? [], JSON_PRETTY_PRINT),
                ]);

                /* ---------------- BUILD COMPONENTS DYNAMICALLY ---------------- */

                $components = [];
                $templateComponents = $template['components'] ?? [];

                // Log template components structure for debugging
                Log::channel('campaign_progress')->debug('Template components structure', [
                    'campaign_id' => $campaign->id,
                    'components_count' => count($templateComponents),
                    'components' => json_encode($templateComponents, JSON_PRETTY_PRINT),
                ]);

                // Build header component based on template structure
                // Check for header component - API might return it as 'HEADER' or 'header'
                $headerComponent = collect($templateComponents)->first(function ($comp) {
                    $type = strtoupper($comp['type'] ?? '');
                    return $type === 'HEADER';
                });

                // Get default image URL from config
                $defaultImageUrl = config('services.whatsapp.default_image_url', 
                    'http://meta.webpayservices.in/WhatsAppMedia/Template/Image/ParidhanWPY/x2iNz14yqSk-MQ(4).jpg');
                $imageUrl = $campaign->image ?? $defaultImageUrl;
                
                // Convert local path to full URL if needed
                if (!empty($campaign->image) && !str_starts_with($imageUrl, 'http')) {
                    $imageUrl = url('uploads/campaign_images/' . $campaign->image);
                }

                // Ensure URL is valid
                if (empty($imageUrl) || !is_string($imageUrl) || !str_starts_with($imageUrl, 'http')) {
                    $imageUrl = $defaultImageUrl;
                }

                if ($headerComponent) {
                    // Try to detect header format from various possible structures
                    $headerFormat = strtoupper($headerComponent['format'] ?? '');
                    
                    // Also check if format is in sub-array or different location
                    if (empty($headerFormat) && isset($headerComponent['example'])) {
                        // Sometimes format info is in example
                        $headerFormat = 'IMAGE'; // Default to IMAGE if example exists
                    }
                    
                    // If still no format, check if there's a text field (TEXT header) or not (likely IMAGE)
                    if (empty($headerFormat)) {
                        if (!empty($headerComponent['text'])) {
                            $headerFormat = 'TEXT';
                        } else {
                            // No text means it's likely IMAGE, VIDEO, or DOCUMENT
                            // Based on error message saying "expected IMAGE", default to IMAGE
                            $headerFormat = 'IMAGE';
                        }
                    }

                    Log::channel('campaign_progress')->info('Building header component', [
                        'campaign_id' => $campaign->id,
                        'header_format' => $headerFormat,
                        'header_component' => json_encode($headerComponent, JSON_PRETTY_PRINT),
                    ]);

                    $headerParameters = [];

                    if ($headerFormat === 'IMAGE') {
                        $headerParameters[] = [
                            'type' => 'image',
                            'image' => [
                                'link' => trim($imageUrl),
                            ],
                        ];
                    } elseif ($headerFormat === 'TEXT' && !empty($headerComponent['text'])) {
                        $headerParameters[] = [
                            'type' => 'text',
                            'text' => $headerComponent['text'],
                        ];
                    } elseif ($headerFormat === 'VIDEO' && !empty($campaign->video_url)) {
                        $headerParameters[] = [
                            'type' => 'video',
                            'video' => [
                                'link' => $campaign->video_url,
                            ],
                        ];
                    } elseif ($headerFormat === 'DOCUMENT' && !empty($campaign->document_url)) {
                        $headerParameters[] = [
                            'type' => 'document',
                            'document' => [
                                'link' => $campaign->document_url,
                            ],
                        ];
                    } else {
                        // Default to IMAGE if format is unknown but header exists
                        // This handles cases where API doesn't return format clearly
                        Log::channel('campaign_progress')->warning('Header format unclear, defaulting to IMAGE', [
                            'campaign_id' => $campaign->id,
                            'detected_format' => $headerFormat,
                            'header_component' => json_encode($headerComponent, JSON_PRETTY_PRINT),
                        ]);
                        $headerParameters[] = [
                            'type' => 'image',
                            'image' => [
                                'link' => trim($imageUrl),
                            ],
                        ];
                    }

                    if (!empty($headerParameters)) {
                        $components[] = [
                            'type' => 'header',
                            'parameters' => $headerParameters,
                        ];
                    }
                } else {
                    // No header component found in template - add IMAGE header as default
                    // This ensures we always have a header if template requires it
                    Log::channel('campaign_progress')->info('No header component in template, adding default IMAGE header', [
                        'campaign_id' => $campaign->id,
                        'template_name' => $templateName,
                    ]);
                    
                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'link' => trim($imageUrl),
                                ],
                            ],
                        ],
                    ];
                }

                // Build body component with variables
                $bodyComponent = collect($templateComponents)->first(function ($comp) {
                    return strtoupper($comp['type'] ?? '') === 'BODY';
                });

                if ($bodyComponent && !empty($bodyComponent['text'])) {
                    // Extract variables from body text ({{1}}, {{2}}, etc.)
                    $bodyText = $bodyComponent['text'];
                    preg_match_all('/\{\{(\d+)\}\}/', $bodyText, $matches);
                    $variableCount = !empty($matches[1]) ? max(array_map('intval', $matches[1])) : 0;

                    // Map campaign data to variables
                    // For now, use campaign name and message as default variables
                    // You can customize this based on your template structure
                    $bodyParameters = [];
                    
                    if ($variableCount >= 1) {
                        $bodyParameters[] = [
                            'type' => 'text',
                            'text' => $campaign->name ?? '',
                        ];
                    }
                    
                    if ($variableCount >= 2) {
                        $bodyParameters[] = [
                            'type' => 'text',
                            'text' => $campaign->message ?? '',
                        ];
                    }
                    
                    // Handle additional variables if template has more than 2
                    for ($i = 3; $i <= $variableCount; $i++) {
                        $bodyParameters[] = [
                            'type' => 'text',
                            'text' => '', // Default empty, you can customize based on campaign data
                        ];
                    }

                    if (!empty($bodyParameters)) {
                        $components[] = [
                            'type' => 'body',
                            'parameters' => $bodyParameters,
                        ];
                    }
                }

                /* ---------------- PAYLOAD ---------------- */

                // Validate components before building payload
                if (empty($components)) {
                    throw new \Exception('No components built for template. Cannot send message without components.');
                }

                $payload = [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $recipient->phone,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => strtolower($template['language'] ?? 'en'),
                        ],
                        'components' => $components,
                    ],
                    'biz_opaque_callback_data' => 'campaign_' . $campaign->id,
                ];

                // Log full payload for debugging
                Log::channel('campaign_progress')->info('Payload built for WhatsApp message', [
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $recipient->id ?? null,
                    'template_name' => $templateName,
                    'components_count' => count($components),
                    'components' => json_encode($components, JSON_PRETTY_PRINT),
                    'full_payload' => json_encode($payload, JSON_PRETTY_PRINT),
                ]);

                /* ---------------- SEND REQUEST ---------------- */

                // Get authentication token (supports both Bearer and X-API-KEY per documentation)
                $apiKey = config('services.whatsapp.api_key');
                $bearerToken = config('services.whatsapp.bearer_token');

                if (empty($apiKey) && empty($bearerToken)) {
                    throw new \Exception('WhatsApp API Key is not configured. Please set WHATSAPP_API_KEY in .env file.');
                }

                // Use Bearer token if available, otherwise use X-API-KEY
                // Working curl example uses: 'authorization: Bearer <token>'
                $headers = [
                    'content-type' => 'application/json',
                ];

                if (!empty($bearerToken)) {
                    $headers['authorization'] = 'Bearer ' . $bearerToken;
                } else {
                    $headers['X-API-KEY'] = $apiKey;
                }

                // Construct endpoint URL according to working curl example:
                // https://meta.webpayservices.in/V23.0/920609244473081/messages
                // Format: {base_url}/{API_VERSION}/{phone_number_id}/messages
                $baseUrl = rtrim(config('services.whatsapp.base_url', 'https://meta.webpayservices.in'), '/');
                $apiVersion = strtoupper(config('services.whatsapp.api_version', 'v23.0')); // Capital V in working example
                $phoneNumberId = config('services.whatsapp.phone_number_id', '920609244473081');
                
                // Use configured endpoint if set, otherwise construct from components
                $endpointUrl = config('services.whatsapp.endpoint');
                if (empty($endpointUrl)) {
                    $endpointUrl = "{$baseUrl}/{$apiVersion}/{$phoneNumberId}/messages";
                }
                
                // Log endpoint and headers before sending
                Log::channel('campaign_progress')->info('Sending WhatsApp message', [
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $recipient->id ?? null,
                    'endpoint' => $endpointUrl,
                    'phone' => $recipient->phone,
                    'template_name' => $payload['template']['name'],
                ]);

                $response = \Illuminate\Support\Facades\Http::withHeaders($headers)->post(
                    $endpointUrl,
                    $payload
                );

                if ($response->successful()) {
                    $status = 'sent';
                    $sent++;

                    Log::channel('campaign_progress')->info('Message sent successfully', [
                        'campaign_id' => $campaign->id,
                        'recipient_id' => $recipient->id,
                        'phone' => $recipient->phone,
                    ]);
                } else {
                    $status = 'failed';
                    $failed++;

                    $responseBody = $response->json();
                    $errorCode = $responseBody['error']['code'] ?? 'unknown';
                    $errorMessage = $responseBody['error']['message'] ?? $response->body();
                    $errorDetails = $responseBody['error']['error_data']['details'] ?? null;

                    $errorContext = [
                        'campaign_id' => $campaign->id,
                        'recipient_id' => $recipient->id,
                        'phone' => $recipient->phone,
                        'http_status' => $response->status(),
                        'error_code' => $errorCode,
                        'error_message' => $errorMessage,
                        'error_details' => $errorDetails,
                        'full_response' => $response->body(),
                        'sent_payload' => json_encode($payload, JSON_PRETTY_PRINT),
                        'header_component' => $components[0] ?? 'NOT_FOUND',
                        'image_url' => $imageUrl,
                    ];

                    // Log to campaign progress
                    Log::channel('campaign_progress')->error('WhatsApp API error', $errorContext);

                    // Also log to dedicated API errors channel
                    Log::channel('whatsapp_api_errors')->error('WhatsApp API error', $errorContext);

                    // Log specific Meta policy violations
                    if ($errorCode == 131049) {
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
    }
}
