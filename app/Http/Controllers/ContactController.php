<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'visitor');
        $filters = $request->only(['search', 'location']);
        $contacts = $this->contactService->getAllContacts($type, $filters);
        
        $title = ucfirst($type) . 's';
        
        return view('contacts.index', compact('contacts', 'type', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'visitor');
        $title = 'Add ' . ucfirst($type);
        
        return view('contacts.create', compact('type', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactRequest $request): JsonResponse
    {
        try {
            $contact = $this->contactService->createContact($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($contact->type) . ' added successfully!',
                'redirect' => route('contacts.index', ['type' => $contact->type])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ' . $request->type . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contact = $this->contactService->getContactById($id);
        $title = 'View ' . ucfirst($contact->type);
        
        return view('contacts.show', compact('contact', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $contact = $this->contactService->getContactById($id);
        $type = $contact->type;
        $title = 'Edit ' . ucfirst($type);
        
        return view('contacts.edit', compact('contact', 'type', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactRequest $request, $id): JsonResponse
    {
        try {
            $contact = $this->contactService->updateContact($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($contact->type) . ' updated successfully!',
                'redirect' => route('contacts.index', ['type' => $contact->type])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $contact = $this->contactService->getContactById($id);
            $type = $contact->type;
            
            $this->contactService->deleteContact($id);
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contact: ' . $e->getMessage()
            ], 500);
        }
    }
}
