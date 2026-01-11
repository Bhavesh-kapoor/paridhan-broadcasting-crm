<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppTemplateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class WhatsAppTemplateController extends Controller
{
    protected $templateService;

    public function __construct(WhatsAppTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Display a listing of templates
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'category']);
        $status = $request->get('status', '');
        
        // Set page title based on status filter
        $pageTitle = 'All Templates';
        if ($status === 'APPROVED') {
            $pageTitle = 'Approved Templates';
        } elseif ($status === 'PENDING') {
            $pageTitle = 'Pending Templates';
        } elseif ($status === 'REJECTED') {
            $pageTitle = 'Rejected Templates';
        } elseif ($status === 'PAUSED') {
            $pageTitle = 'Paused Templates';
        }
        
        return view('whatsapp-templates.index', compact('filters', 'status', 'pageTitle'));
    }

    /**
     * Get templates list for DataTables
     */
    public function getAllTemplatesList(Request $request)
    {
        $filters = $request->only(['search', 'status', 'category']);
        $templates = $this->templateService->getAllTemplates($filters);

        return DataTables::of($templates)
            ->addIndexColumn()
            ->addColumn('template_name', function ($template) {
                return '<strong>' . $template->name . '</strong><br>
                        <small class="text-muted">Language: ' . strtoupper($template->language) . '</small>';
            })
            ->addColumn('body_text', function ($template) {
                $bodyText = $template->body_text;
                return strlen($bodyText) > 100 
                    ? substr($bodyText, 0, 100) . '...' 
                    : $bodyText;
            })
            ->addColumn('category_badge', function ($template) {
                return '<span class="badge ' . $template->category_badge_class . ' px-3 py-2 border">
                            ' . $template->category . '
                        </span>';
            })
            ->addColumn('status_badge', function ($template) {
                $icon = match($template->status) {
                    'APPROVED' => 'bx-check-circle',
                    'PENDING' => 'lni-alarm-clock',
                    'REJECTED' => 'bx-x-circle',
                    'PAUSED' => 'bx-pause-circle',
                    default => 'bx-file'
                };
                return '<span class="badge ' . $template->status_badge_class . ' px-3 py-2 border">
                            <i class="' . $icon . ' me-1"></i>' . $template->status . '
                        </span>';
            })
            ->addColumn('synced_at', function ($template) {
                return $template->synced_at 
                    ? $template->synced_at->format('M d, Y h:i A')
                    : 'Never';
            })
            ->addColumn('action', function ($template) {
                $buttons = '<button class="btn btn-info btn-sm btnView" 
                            data-template-id="' . $template->id . '" 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="bottom" 
                            title="View Template">
                            <i class="lni lni-eye"></i>
                        </button>';

                if ($template->status === 'APPROVED') {
                    $buttons .= '<button class="btn btn-success btn-sm ms-1 useTemplate" 
                                data-template-name="' . $template->name . '" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="bottom" 
                                title="Use in Campaign">
                                <i class="bx bx-check"></i>
                            </button>';
                }

                return $buttons;
            })
            ->rawColumns(['template_name', 'category_badge', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Sync templates from WhatsApp API
     */
    public function sync(Request $request): JsonResponse
    {
        try {
            $result = $this->templateService->fetchTemplatesFromAPI();

            if ($result['status']) {
                return response()->json([
                    'status' => true,
                    'message' => $result['message'],
                    'count' => $result['count'] ?? 0,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'],
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to sync templates: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get approved templates for campaign creation
     */
    public function getApprovedTemplates(): JsonResponse
    {
        try {
            $templates = $this->templateService->getApprovedTemplates();

            return response()->json([
                'status' => true,
                'templates' => $templates->map(function ($template) {
                    return [
                        'id' => $template->id,
                        'name' => $template->name,
                        'language' => $template->language,
                        'category' => $template->category,
                        'body_text' => $template->body_text,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch templates: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified template
     */
    public function show($id)
    {
        $template = $this->templateService->getTemplateById($id);
        return view('whatsapp-templates.show', compact('template'));
    }

    /**
     * Get template details (AJAX)
     */
    public function getTemplateDetails($id): JsonResponse
    {
        try {
            $template = $this->templateService->getTemplateById($id);

            return response()->json([
                'status' => true,
                'template' => [
                    'id' => $template->id,
                    'template_id' => $template->template_id,
                    'name' => $template->name,
                    'language' => $template->language,
                    'category' => $template->category,
                    'status' => $template->status,
                    'components' => $template->components,
                    'body_text' => $template->body_text,
                    'synced_at' => $template->synced_at?->format('M d, Y h:i A'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Template not found: ' . $e->getMessage(),
            ], 404);
        }
    }
}
