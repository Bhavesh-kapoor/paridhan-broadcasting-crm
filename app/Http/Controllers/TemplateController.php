<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TemplateController extends Controller
{
    /**
     * Display the templates list page
     */
    public function index()
    {
        return view('templates.index');
    }

    /**
     * Fetch templates from Meta API via AJAX
     */
    public function getTemplates(Request $request)
    {
        try {
            // Fetch templates (with caching, but don't cache errors)
            $cachedTemplates = Cache::get('whatsapp_templates');
            
            if ($cachedTemplates === null || $request->has('refresh')) {
                // Fetch fresh data from API
                $templates = $this->fetchTemplatesFromAPI();
                
                // Only cache if successful (no error)
                if (!isset($templates['error']) && is_array($templates)) {
                    Cache::put('whatsapp_templates', $templates, 300);
                } else {
                    // If there's an error, try to use cached data if available
                    if ($cachedTemplates !== null && is_array($cachedTemplates) && !isset($cachedTemplates['error'])) {
                        $templates = $cachedTemplates;
                    }
                }
            } else {
                $templates = $cachedTemplates;
            }

            // Check if there's an error in the response
            if (isset($templates['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $templates['error'],
                    'data' => []
                ], 500);
            }

            // Ensure templates is an array
            if (!is_array($templates)) {
                $templates = [];
            }

            // Format data for DataTables - Include ALL templates with ALL statuses
            $formattedTemplates = collect($templates)->map(function ($template) {
                // Normalize status - handle different possible status values
                $status = strtoupper($template['status'] ?? 'UNKNOWN');
                
                // Map various status values to standard ones
                $statusMap = [
                    'APPROVED' => 'APPROVED',
                    'PENDING' => 'PENDING',
                    'REJECTED' => 'REJECTED',
                    'PENDING_DELETION' => 'PENDING_DELETION',
                    'DELETED' => 'DELETED',
                    'LIMITED' => 'LIMITED',
                    'PAUSED' => 'PAUSED',
                    'UNKNOWN' => 'UNKNOWN'
                ];
                
                $normalizedStatus = $statusMap[$status] ?? 'UNKNOWN';
                
                // Parse created_time (can be timestamp or ISO string)
                $createdAt = 'N/A';
                if (isset($template['created_time'])) {
                    if (is_numeric($template['created_time'])) {
                        $createdAt = date('Y-m-d H:i:s', $template['created_time']);
                    } else {
                        try {
                            $createdAt = date('Y-m-d H:i:s', strtotime($template['created_time']));
                        } catch (\Exception $e) {
                            $createdAt = $template['created_time'];
                        }
                    }
                }
                
                return [
                    'id' => $template['id'] ?? $template['name'] ?? 'N/A',
                    'name' => $template['name'] ?? 'N/A',
                    'language' => strtoupper($template['language'] ?? 'en'),
                    'status' => $normalizedStatus,
                    'category' => $template['category'] ?? 'N/A',
                    'created_at' => $createdAt,
                    'components' => $template['components'] ?? [],
                    'quality_score' => $template['quality_score'] ?? null,
                    'rejection_reason' => $template['rejection_reason'] ?? null,
                ];
            });
            
            // Sort by created_at descending (newest first) and then by status
            $formattedTemplates = $formattedTemplates->sortByDesc(function ($template) {
                return [$template['status'], $template['created_at']];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $formattedTemplates
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch WhatsApp templates', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch templates: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Display template details
     */
    public function show($id)
    {
        try {
            $templates = Cache::get('whatsapp_templates', []);

            $template = collect($templates)->firstWhere('id', $id);

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $template
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch template details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch templates from Meta API
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
                throw new \Exception('WhatsApp API Key is not configured. Please set WHATSAPP_API_KEY in .env file.');
            }

            if (empty($wabaId)) {
                throw new \Exception('WABA ID is not configured. Please set WHATSAPP_WABA_ID in .env file.');
            }
            
            // Validate WABA ID - according to API docs, it should be different from Phone Number ID
            $phoneNumberId = config('services.whatsapp.phone_number_id');
            if ($wabaId === $phoneNumberId && !empty($phoneNumberId)) {
                Log::warning('WABA ID matches Phone Number ID - this is likely incorrect', [
                    'waba_id' => $wabaId,
                    'phone_number_id' => $phoneNumberId,
                    'note' => 'According to the API documentation, WABA ID and Phone Number ID should be different. Template endpoints require the actual WABA ID, not the Phone Number ID. Please verify WHATSAPP_WABA_ID in .env file with your API provider.'
                ]);
            }

            // Build endpoint URL according to API documentation:
            // GET /{version}/{wabaId}/message_templates
            // Reference: http://meta.webpayservices.in/developer/console.html?api=whatsapp#get-/-version-/-wabaId-/message_templates
            if (!empty($templateEndpoint)) {
                // Replace placeholders in template_endpoint
                // Support both {version} and {wabaId} placeholders
                $endpoint = str_replace(
                    ['{version}', '{wabaId}', '{apiVersion}', '{waba_id}'],
                    [$apiVersion, $wabaId, $apiVersion, $wabaId],
                    $templateEndpoint
                );
            } else {
                // Fallback to manual construction following API documentation format
                if (empty($baseUrl)) {
                    throw new \Exception('WhatsApp Base URL is not configured. Please set WHATSAPP_BASE_URL in .env file.');
                }
                
                // Remove trailing slash from baseUrl if present
                $baseUrl = rtrim($baseUrl, '/');
                // Construct: base_url/{version}/{wabaId}/message_templates
                $endpoint = "{$baseUrl}/{$apiVersion}/{$wabaId}/message_templates";
            }
            
            // Log the endpoint being used for debugging
            Log::debug('Template endpoint construction', [
                'template_endpoint_config' => $templateEndpoint,
                'base_url' => $baseUrl,
                'api_version' => $apiVersion,
                'waba_id' => $wabaId,
                'final_endpoint' => $endpoint
            ]);

            // Set authentication headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            if (!empty($bearerToken)) {
                $headers['Authorization'] = 'Bearer ' . $bearerToken;
            } elseif (!empty($apiKey)) {
                $headers['X-API-KEY'] = $apiKey;
            }

            Log::info('Fetching WhatsApp templates from API', [
                'endpoint' => $endpoint,
                'waba_id' => $wabaId,
                'api_version' => $apiVersion,
                'base_url' => $baseUrl,
            ]);

            // Make API request following the API documentation format:
            // GET /{version}/{wabaId}/message_templates
            // Reference: http://meta.webpayservices.in/developer/console.html?api=whatsapp#get-/-version-/-wabaId-/message_templates
            // Fetch ALL templates regardless of status (APPROVED, PENDING, REJECTED, PENDING_DELETION, etc.)
            $response = Http::withHeaders($headers)
                ->timeout(60) // Increased timeout for fetching all templates
                ->get($endpoint, [
                    'limit' => 1000, // Request up to 1000 templates to get all
                    // Note: We intentionally don't filter by status to get ALL templates
                    // Some APIs may support: 'status' => 'APPROVED,PENDING,REJECTED'
                    // But we want everything, so we don't specify status filter
                ]);
            
            // If WABA ID matches Phone Number ID and request fails, try with default WABA ID from config
            // This handles cases where .env has wrong WABA ID
            if (!$response->successful() && $response->status() === 500 && $wabaId === $phoneNumberId) {
                $defaultWabaId = '735434666284028'; // Default from config/services.php
                
                if ($defaultWabaId !== $wabaId) {
                    Log::info('Trying with default WABA ID since current WABA ID matches Phone Number ID', [
                        'current_waba_id' => $wabaId,
                        'trying_default' => $defaultWabaId,
                        'original_endpoint' => $endpoint
                    ]);
                    
                    // Build endpoint with default WABA ID
                    $defaultEndpoint = str_replace(
                        [$wabaId],
                        [$defaultWabaId],
                        $endpoint
                    );
                    
                    $response = Http::withHeaders($headers)
                        ->timeout(30)
                        ->get($defaultEndpoint);
                    
                    if ($response->successful()) {
                        Log::info('Successfully fetched templates using default WABA ID - update your .env file!', [
                            'default_waba_id' => $defaultWabaId,
                            'endpoint' => $defaultEndpoint
                        ]);
                        
                        // Update endpoint for logging
                        $endpoint = $defaultEndpoint;
                        $wabaId = $defaultWabaId;
                    }
                }
            }
            
            // If still failing, log detailed information
            if (!$response->successful() && $response->status() === 500) {
                Log::warning('API returned 500 error - configuration issue likely', [
                    'endpoint' => $endpoint,
                    'waba_id' => $wabaId,
                    'note' => '500 errors usually indicate: (1) Invalid WABA ID - verify WHATSAPP_WABA_ID in .env, (2) Authentication issue, or (3) API server problem.'
                ]);
            }

            if ($response->successful()) {
                $data = $response->json();

                // Handle different response structures
                // Meta API might return data directly or wrapped in a 'data' key
                $templates = [];
                
                if (isset($data['data']) && is_array($data['data'])) {
                    // Standard Meta API response structure with pagination
                    $templates = $data['data'];
                    
                    // Handle pagination if present (some APIs return next page token)
                    // Note: For now, we fetch up to 1000 templates. If you need more, implement pagination.
                    if (isset($data['paging']['next'])) {
                        Log::info('More templates available via pagination', [
                            'current_count' => count($templates),
                            'next_page' => $data['paging']['next']
                        ]);
                    }
                } elseif (is_array($data)) {
                    // Response might be an array directly
                    if (isset($data[0]) && is_array($data[0])) {
                        // Array of template objects
                        $templates = $data;
                    } else {
                        // Single template object wrapped in array
                        $templates = [$data];
                    }
                }

                // Count templates by status
                $statusCounts = [];
                foreach ($templates as $template) {
                    $status = strtoupper($template['status'] ?? 'UNKNOWN');
                    $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
                }

                Log::info('Successfully fetched WhatsApp templates', [
                    'total_count' => count($templates),
                    'status_breakdown' => $statusCounts,
                    'response_structure' => array_keys($data ?? [])
                ]);

                return $templates;
            } else {
                $statusCode = $response->status();
                $errorBody = $response->json();
                $rawBody = $response->body();
                
                // Extract error message from different possible structures
                // Custom API wrapper format: {"statusCode":500,"message":"...","path":"...","details":null}
                // Standard Meta API format: {"error":{"message":"...","code":...}}
                $errorMessage = 'Unknown error';
                
                if (is_array($errorBody)) {
                    // Try custom API wrapper format first
                    if (isset($errorBody['message'])) {
                        $errorMessage = $errorBody['message'];
                    } elseif (isset($errorBody['error']['message'])) {
                        // Standard Meta API format
                        $errorMessage = $errorBody['error']['message'];
                    } elseif (isset($errorBody['error'])) {
                        $errorMessage = is_string($errorBody['error']) ? $errorBody['error'] : json_encode($errorBody['error']);
                    }
                }
                
                if ($errorMessage === 'Unknown error' || empty($errorMessage)) {
                    $errorMessage = !empty($rawBody) ? $rawBody : 'No error details provided';
                }

                // Enhance error message for 500 errors with helpful suggestions based on API documentation
                // Reference: http://meta.webpayservices.in/developer/console.html?api=whatsapp#get-/-version-/-wabaId-/message_templates
                $enhancedErrorMessage = $errorMessage;
                if ($statusCode === 500) {
                    $enhancedErrorMessage .= '. Common causes: (1) Invalid WABA ID - According to API docs, use the actual WABA ID (not Phone Number ID). Your current WABA ID: ' . $wabaId . 
                        '. (2) Authentication - Verify your API key/bearer token has template read permissions. (3) API server - Contact meta.webpayservices.in support if issue persists.';
                    
                    // Add specific guidance if WABA ID matches Phone Number ID
                    if (isset($phoneNumberId) && $wabaId === $phoneNumberId) {
                        $enhancedErrorMessage .= ' NOTE: Your WABA ID appears to be the same as Phone Number ID. Template endpoints require the actual WABA ID, not the Phone Number ID.';
                    }
                }

                Log::error('Meta API error while fetching templates', [
                    'status' => $statusCode,
                    'error' => $errorMessage,
                    'response' => $rawBody,
                    'endpoint' => $endpoint,
                    'headers_used' => array_keys($headers), // Don't log actual auth tokens
                    'waba_id' => $wabaId,
                    'api_version' => $apiVersion,
                    'has_bearer_token' => !empty($bearerToken),
                    'has_api_key' => !empty($apiKey),
                ]);

                return [
                    'error' => "API Error ({$statusCode}): {$enhancedErrorMessage}"
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception while fetching templates from API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Refresh templates cache
     */
    public function refreshCache()
    {
        try {
            Cache::forget('whatsapp_templates');

            $templates = $this->fetchTemplatesFromAPI();

            if (isset($templates['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $templates['error']
                ], 500);
            }

            Cache::put('whatsapp_templates', $templates, 300);

            return response()->json([
                'success' => true,
                'message' => 'Templates cache refreshed successfully',
                'count' => count($templates)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created template via Meta API
     * Following WhatsApp Business API template creation guidelines
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:3|max:512|regex:/^[a-z0-9_]+$/',
                'language' => 'required|string|max:10',
                'category' => 'required|in:MARKETING,UTILITY,AUTHENTICATION',
                'header_type' => 'nullable|in:TEXT,IMAGE,VIDEO,DOCUMENT',
                'header_text' => 'nullable|string|max:60',
                'body_text' => 'required|string|min:1|max:1024',
                'footer_text' => 'nullable|string|max:60',
                'buttons' => 'nullable|json',
            ], [
                'name.regex' => 'Template name must contain only lowercase letters, numbers, and underscores',
                'name.min' => 'Template name must be at least 3 characters',
                'name.max' => 'Template name cannot exceed 512 characters',
                'body_text.max' => 'Body text cannot exceed 1024 characters',
                'header_text.max' => 'Header text cannot exceed 60 characters',
                'footer_text.max' => 'Footer text cannot exceed 60 characters',
            ]);

            // Validate template name format
            if (!preg_match('/^[a-z0-9_]+$/', $validated['name'])) {
                return back()->withInput()
                    ->withErrors(['name' => 'Template name must contain only lowercase letters, numbers, and underscores']);
            }

            // Validate variables in body text are sequential
            $bodyText = $validated['body_text'];
            preg_match_all('/\{\{(\d+)\}\}/', $bodyText, $matches);
            if (!empty($matches[1])) {
                $variables = array_unique(array_map('intval', $matches[1]));
                sort($variables);
                $expectedVariables = range(1, count($variables));
                
                if ($variables !== $expectedVariables) {
                    return back()->withInput()
                        ->withErrors(['body_text' => 'Variables must be sequential starting from {{1}}. Example: {{1}}, {{2}}, {{3}}...']);
                }
            }

            // Validate header text if header type is TEXT
            if ($validated['header_type'] === 'TEXT') {
                if (empty($validated['header_text']) || trim($validated['header_text']) === '') {
                    return back()->withInput()
                        ->withErrors(['header_text' => 'Header text is required when header type is TEXT. Please enter header text or select a different header type.']);
                }
                
                // Validate header text length
                if (strlen($validated['header_text']) > 60) {
                    return back()->withInput()
                        ->withErrors(['header_text' => 'Header text cannot exceed 60 characters.']);
                }
            }

            // Validate footer doesn't contain variables (not allowed per API)
            if (!empty($validated['footer_text']) && preg_match('/\{\{\d+\}\}/', $validated['footer_text'])) {
                return back()->withInput()
                    ->withErrors(['footer_text' => 'Footer text cannot contain variables']);
            }

            // Build components array according to API documentation
            $components = [];

            // Header component (optional)
            if (!empty($validated['header_type'])) {
                $headerComponent = [
                    'type' => 'HEADER',
                    'format' => $validated['header_type']
                ];

                if ($validated['header_type'] === 'TEXT') {
                    // Ensure header text is provided and not empty
                    if (empty($validated['header_text']) || trim($validated['header_text']) === '') {
                        return back()->withInput()
                            ->withErrors(['header_text' => 'Header text is required when header type is TEXT.']);
                    }
                    $headerComponent['text'] = trim($validated['header_text']);
                }
                // For IMAGE, VIDEO, DOCUMENT - media URL will be provided during submission
                // Note: This implementation assumes the API handles media uploads separately
                // If your API requires media URLs in the payload, you may need to adjust this

                $components[] = $headerComponent;
            }

            // Body component (required) - supports variables {{1}}, {{2}}, etc.
            $components[] = [
                'type' => 'BODY',
                'text' => $validated['body_text']
            ];

            // Footer component (optional) - cannot contain variables
            if (!empty($validated['footer_text'])) {
                $components[] = [
                    'type' => 'FOOTER',
                    'text' => $validated['footer_text']
                ];
            }

            // Buttons component (optional)
            if (!empty($validated['buttons'])) {
                $buttons = json_decode($validated['buttons'], true);
                if (!empty($buttons) && is_array($buttons)) {
                    $components[] = [
                        'type' => 'BUTTONS',
                        'buttons' => $buttons
                    ];
                }
            }

            // Prepare API payload according to WhatsApp Business API format
            // Based on API documentation: POST /message_templates
            // Expected format: {"name":"string","category":"string","allow_category_change":false,"language":"string","components":[...]}
            $payload = [
                'name' => strtolower(trim($validated['name'])), // Ensure lowercase
                'category' => $validated['category'], // MARKETING, UTILITY, or AUTHENTICATION
                'allow_category_change' => false, // Set to false as per API requirements
                'language' => strtolower(trim($validated['language'])), // Language code (e.g., 'en', 'hi')
                'components' => $components // Array of template components (HEADER, BODY, FOOTER, BUTTONS)
            ];
            
            // Optional fields - only include if they have values
            // library_template_name and LIBRARY_TEMPLATE_BUTTON_INPUTS are optional
            // We're not using library templates in this implementation, so we skip them

            Log::info('Template creation payload prepared', [
                'template_name' => $payload['name'],
                'category' => $payload['category'],
                'components_count' => count($components)
            ]);

            // Note: We don't pre-check for duplicates in cache because:
            // 1. Cache might be stale
            // 2. Cache might contain error responses
            // 3. API is the source of truth
            // Let the API handle duplicate detection and return proper error messages

            // Make API request
            $result = $this->createTemplateViaAPI($payload);

            if ($result['success']) {
                // Clear cache to reflect new template
                Cache::forget('whatsapp_templates');

                return redirect()->route('templates.index')
                    ->with('success', 'Template "' . $payload['name'] . '" created successfully! It will be reviewed by Meta and may take 24-48 hours to be approved. You can check its status in the templates list.');
            } else {
                // Check if error is about template already existing
                $errorMessage = $result['message'] ?? '';
                $isTemplateExistsError = stripos($errorMessage, 'already exists') !== false ||
                                        stripos($errorMessage, 'name is already') !== false ||
                                        stripos($errorMessage, '132000') !== false ||
                                        stripos($errorMessage, '131048') !== false;
                
                if ($isTemplateExistsError) {
                    // Clear cache and refresh templates to show the existing one
                    Cache::forget('whatsapp_templates');
                    
                    return back()->withInput()
                        ->withErrors(['name' => 'A template with this name already exists. The template has been refreshed in the listing. Please check the templates list or use a different name.'])
                        ->with('error', 'Template name "' . $payload['name'] . '" already exists. Please check the templates list or use a different name.');
                }
                
                return back()->withInput()
                    ->withErrors(['error' => $result['message']])
                    ->with('error', $result['message']);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Failed to create template', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token'])
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Failed to create template: ' . $e->getMessage()])
                ->with('error', 'Failed to create template: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a template
     */
    public function edit($id)
    {
        try {
            $templates = Cache::get('whatsapp_templates', []);
            $template = collect($templates)->firstWhere('id', $id);

            if (!$template) {
                return redirect()->route('templates.index')
                    ->with('error', 'Template not found');
            }

            return view('templates.edit', compact('template'));

        } catch (\Exception $e) {
            return redirect()->route('templates.index')
                ->with('error', 'Failed to load template: ' . $e->getMessage());
        }
    }

    /**
     * Update a template via Meta API
     */
    public function update(Request $request, $id)
    {
        try {
            // Note: Meta API typically doesn't allow editing approved templates
            // You need to delete and recreate them
            // This method will handle the update logic if supported by your API

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'language' => 'required|string|max:10',
                'category' => 'required|in:MARKETING,UTILITY,AUTHENTICATION',
                'body_text' => 'required|string',
            ]);

            // Build update payload
            $payload = [
                'name' => $validated['name'],
                'language' => $validated['language'],
                'category' => $validated['category'],
            ];

            $result = $this->updateTemplateViaAPI($id, $payload);

            if ($result['success']) {
                Cache::forget('whatsapp_templates');

                return redirect()->route('templates.index')
                    ->with('success', 'Template updated successfully!');
            } else {
                return back()->withInput()
                    ->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('Failed to update template', [
                'template_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update template: ' . $e->getMessage());
        }
    }

    /**
     * Delete a template via Meta API
     */
    public function destroy($id)
    {
        try {
            $result = $this->deleteTemplateViaAPI($id);

            if ($result['success']) {
                // Clear cache
                Cache::forget('whatsapp_templates');

                return response()->json([
                    'status' => true,
                    'message' => 'Template deleted successfully!'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to delete template', [
                'template_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create template via Meta API
     * Following API documentation: POST /{version}/{wabaId}/message_templates
     * Reference: http://meta.webpayservices.in/developer/console.html
     */
    private function createTemplateViaAPI($payload)
    {
        try {
            $apiKey = config('services.whatsapp.api_key');
            $bearerToken = config('services.whatsapp.bearer_token');
            $wabaId = config('services.whatsapp.waba_id');
            $apiVersion = config('services.whatsapp.api_version', 'v23.0');
            $baseUrl = config('services.whatsapp.base_url');

            if (empty($apiKey) && empty($bearerToken)) {
                throw new \Exception('WhatsApp API Key is not configured. Please set WHATSAPP_API_KEY in .env file.');
            }

            if (empty($wabaId)) {
                throw new \Exception('WABA ID is not configured. Please set WHATSAPP_WABA_ID in .env file.');
            }

            // Build endpoint according to API documentation
            // POST /{version}/{wabaId}/message_templates
            $baseUrl = rtrim($baseUrl, '/');
            $endpoint = "{$baseUrl}/{$apiVersion}/{$wabaId}/message_templates";

            // Set authentication headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            if (!empty($bearerToken)) {
                $headers['Authorization'] = 'Bearer ' . $bearerToken;
            } elseif (!empty($apiKey)) {
                $headers['X-API-KEY'] = $apiKey;
            }

            Log::info('Creating WhatsApp template via API', [
                'endpoint' => $endpoint,
                'waba_id' => $wabaId,
                'api_version' => $apiVersion,
                'template_name' => $payload['name'] ?? 'N/A',
                'category' => $payload['category'] ?? 'N/A',
                'language' => $payload['language'] ?? 'N/A',
                'allow_category_change' => $payload['allow_category_change'] ?? false,
                'components_count' => count($payload['components'] ?? []),
                'payload_structure' => array_keys($payload)
            ]);

            // Make API request - ensure payload matches API format exactly
            $response = Http::withHeaders($headers)
                ->timeout(60) // Increased timeout for template creation
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('Template created successfully', [
                    'template_name' => $payload['name'] ?? 'N/A',
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'data' => $responseData
                ];
            } else {
                $statusCode = $response->status();
                $errorBody = $response->json();
                $rawBody = $response->body();

                // Extract error message from different possible structures
                $errorMessage = 'Unknown error';
                $errorCode = null;
                
                if (is_array($errorBody)) {
                    // Custom API wrapper format: {"statusCode":500,"message":"...","path":"...","details":null}
                    if (isset($errorBody['message'])) {
                        $errorMessage = $errorBody['message'];
                        // Check for error code in details or other fields
                        if (isset($errorBody['details']) && is_array($errorBody['details'])) {
                            $errorCode = $errorBody['details']['code'] ?? null;
                        }
                    } elseif (isset($errorBody['error']['message'])) {
                        // Standard Meta API format: {"error":{"message":"...","code":...}}
                        $errorMessage = $errorBody['error']['message'];
                        // Include error code if available
                        if (isset($errorBody['error']['code'])) {
                            $errorCode = $errorBody['error']['code'];
                            $errorMessage = "[Code: {$errorCode}] {$errorMessage}";
                        }
                    } elseif (isset($errorBody['error'])) {
                        $errorMessage = is_string($errorBody['error']) ? $errorBody['error'] : json_encode($errorBody['error']);
                        if (is_array($errorBody['error']) && isset($errorBody['error']['code'])) {
                            $errorCode = $errorBody['error']['code'];
                        }
                    }
                }
                
                if ($errorMessage === 'Unknown error' || empty($errorMessage)) {
                    $errorMessage = !empty($rawBody) ? $rawBody : 'No error details provided';
                }
                
                // Check for template name already exists in error message
                // Only mark as "template exists" error if we have clear indicators
                $isTemplateExistsError = false;
                
                // Check error code first (most reliable)
                if ($errorCode == 132000 || $errorCode == 131048) {
                    $isTemplateExistsError = true;
                }
                
                // Check error message for keywords (but be careful not to match generic 500 errors)
                if (!$isTemplateExistsError) {
                    $errorMessageLower = strtolower($errorMessage);
                    $isTemplateExistsError = (
                        (stripos($errorMessage, 'already exists') !== false && stripos($errorMessage, 'template') !== false) ||
                        (stripos($errorMessage, 'name is already') !== false) ||
                        (stripos($errorMessage, 'duplicate') !== false && stripos($errorMessage, 'template') !== false) ||
                        (stripos($errorMessage, '132000') !== false) ||
                        (stripos($errorMessage, '131048') !== false)
                    ) && stripos($errorMessage, 'internal server error') === false; // Exclude generic 500 errors
                }
                
                // Add helpful messages for common error codes
                if ($errorCode !== null) {
                    $errorMessages = [
                        100 => 'Invalid parameter - Check template variables format ({{1}}, {{2}}, etc.)',
                        132000 => 'Template name already exists or template not found',
                        131048 => 'Template name is already in use',
                        131047 => 'Template name was recently deleted (cannot reuse for 30 days)',
                        131026 => 'Invalid template structure or missing required components',
                    ];
                    
                    if (isset($errorMessages[$errorCode])) {
                        $errorMessage .= '. ' . $errorMessages[$errorCode];
                    }
                }

                // Enhance error message with helpful suggestions
                if ($statusCode === 500) {
                    if ($isTemplateExistsError) {
                        $errorMessage = 'Template name already exists. ' . $errorMessage . ' Please check the templates list - the existing template will be shown there.';
                    } else {
                        $errorMessage .= '. Possible causes: (1) Invalid WABA ID - verify WHATSAPP_WABA_ID in .env file, (2) Authentication issue - check API key/bearer token, (3) Template name already exists, (4) Invalid template structure - verify all components are properly formatted.';
                    }
                } elseif ($statusCode === 400) {
                    $errorMessage .= '. Please check: (1) Template name format (lowercase, alphanumeric, underscores only), (2) Variable format ({{1}}, {{2}}, etc. - must be sequential), (3) Character limits (Body: 1024, Header: 60, Footer: 60), (4) Required components (Body is mandatory), (5) Header text required when header type is TEXT.';
                }

                Log::error('Meta API error while creating template', [
                    'status' => $statusCode,
                    'error' => $errorMessage,
                    'error_code' => $errorCode,
                    'is_template_exists_error' => $isTemplateExistsError,
                    'response' => $rawBody,
                    'endpoint' => $endpoint,
                    'payload_template_name' => $payload['name'] ?? 'N/A',
                ]);

                // If template exists error, clear cache so user can see it in listing
                if ($isTemplateExistsError) {
                    Cache::forget('whatsapp_templates');
                }

                return [
                    'success' => false,
                    'message' => "API Error ({$statusCode}): {$errorMessage}",
                    'is_template_exists_error' => $isTemplateExistsError
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception while creating template', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create template: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update template via Meta API
     */
    private function updateTemplateViaAPI($id, $payload)
    {
        try {
            $apiKey = config('services.whatsapp.api_key');
            $bearerToken = config('services.whatsapp.bearer_token');
            $apiVersion = config('services.whatsapp.api_version', 'v23.0');
            $baseUrl = config('services.whatsapp.base_url');

            $endpoint = "{$baseUrl}/{$apiVersion}/{$id}";

            $headers = ['Content-Type' => 'application/json'];
            if (!empty($bearerToken)) {
                $headers['Authorization'] = 'Bearer ' . $bearerToken;
            } else {
                $headers['X-API-KEY'] = $apiKey;
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? $response->body();

                return [
                    'success' => false,
                    'message' => "API Error ({$response->status()}): {$errorMessage}"
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete template via Meta API
     */
    private function deleteTemplateViaAPI($id)
    {
        try {
            $apiKey = config('services.whatsapp.api_key');
            $bearerToken = config('services.whatsapp.bearer_token');
            $wabaId = config('services.whatsapp.waba_id');
            $apiVersion = config('services.whatsapp.api_version', 'v23.0');
            $baseUrl = config('services.whatsapp.base_url');

            // Meta API endpoint for deleting templates
            $endpoint = "{$baseUrl}/{$apiVersion}/{$wabaId}/message_templates";

            $headers = ['Content-Type' => 'application/json'];
            if (!empty($bearerToken)) {
                $headers['Authorization'] = 'Bearer ' . $bearerToken;
            } else {
                $headers['X-API-KEY'] = $apiKey;
            }

            Log::info('Deleting WhatsApp template via API', [
                'endpoint' => $endpoint,
                'template_id' => $id
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->delete($endpoint, [
                    'name' => $id  // Template name or ID
                ]);

            if ($response->successful()) {
                Log::info('Template deleted successfully', [
                    'template_id' => $id
                ]);

                return [
                    'success' => true
                ];
            } else {
                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? $response->body();

                Log::error('Meta API error while deleting template', [
                    'status' => $response->status(),
                    'error' => $errorMessage
                ]);

                return [
                    'success' => false,
                    'message' => "API Error ({$response->status()}): {$errorMessage}"
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception while deleting template', [
                'template_id' => $id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
