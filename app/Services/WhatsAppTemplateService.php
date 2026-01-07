<?php

namespace App\Services;

use App\Models\WhatsAppTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppTemplateService
{
    /**
     * Fetch templates from WhatsApp API
     */
    public function fetchTemplatesFromAPI()
    {
        try {
            $baseUrl = config('services.whatsapp.base_url');
            $version = config('services.whatsapp.api_version', 'v23.0');
            $wabaId = config('services.whatsapp.waba_id');
            $bearerToken = config('services.whatsapp.bearer_token') ?? config('services.whatsapp.api_key');

            if (empty($wabaId)) {
                throw new \Exception('WABA ID is not configured. Please set WHATSAPP_WABA_ID in .env file.');
            }

            if (empty($bearerToken)) {
                throw new \Exception('WhatsApp API Key is not configured. Please set WHATSAPP_API_KEY in .env file.');
            }

            $url = "{$baseUrl}/{$version}/{$wabaId}/message_templates";

            Log::info('Fetching templates from WhatsApp API', [
                'url' => $url,
                'waba_id' => $wabaId,
            ]);

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $bearerToken,
                'Content-Type' => 'application/json',
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $templates = $data['data'] ?? [];

                $synced = 0;
                foreach ($templates as $templateData) {
                    $this->syncTemplate($templateData);
                    $synced++;
                }

                return [
                    'status' => true,
                    'message' => "Successfully synced {$synced} templates",
                    'count' => $synced,
                ];
            } else {
                $error = $response->json();
                Log::error('WhatsApp Template API Error', [
                    'status' => $response->status(),
                    'response' => $error,
                ]);

                return [
                    'status' => false,
                    'message' => $error['error']['message'] ?? 'Failed to fetch templates',
                    'error' => $error,
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Template Fetch Exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sync template data to database
     */
    protected function syncTemplate(array $templateData)
    {
        try {
            WhatsAppTemplate::updateOrCreate(
                [
                    'template_id' => $templateData['id'] ?? null,
                ],
                [
                    'name' => $templateData['name'] ?? '',
                    'language' => $templateData['language'] ?? 'en',
                    'category' => strtoupper($templateData['category'] ?? 'UTILITY'),
                    'status' => strtoupper($templateData['status'] ?? 'PENDING'),
                    'components' => json_encode($templateData['components'] ?? []),
                    'allow_category_change' => $templateData['allow_category_change'] ?? false,
                    'synced_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error syncing template', [
                'template_data' => $templateData,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get all templates
     */
    public function getAllTemplates($filters = [])
    {
        $query = WhatsAppTemplate::query();

        if (!empty($filters['status'])) {
            $query->where('status', strtoupper($filters['status']));
        }

        if (!empty($filters['category'])) {
            $query->where('category', strtoupper($filters['category']));
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('language', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get approved templates only
     */
    public function getApprovedTemplates()
    {
        return WhatsAppTemplate::where('status', 'APPROVED')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get template by ID
     */
    public function getTemplateById($id)
    {
        return WhatsAppTemplate::findOrFail($id);
    }

    /**
     * Get template by template_id (WhatsApp API ID)
     */
    public function getTemplateByTemplateId($templateId)
    {
        return WhatsAppTemplate::where('template_id', $templateId)->first();
    }
}

