<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

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
    public function index(Request $request): View
    {
        $type = $request->get('type', 'visitor');
        if ($type=="exhibitor") {
           return view('exhibitor.index');
        } else {
             return view('visitors.index');

        }
    }

    /**
     * server side rendoring data table
     */

    public function getAllContactsList(Request $request): JsonResponse
    {
        $type = $request->type;
        return $this->contactService->getAllContactsList($type);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
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
                'status' => true,
                'message' => ucfirst($contact->type) . ' added successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create ' . $request->type . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $contact = $this->contactService->getContactById($id);
        $title = 'View ' . ucfirst($contact->type);

        return view('contacts.show', compact('contact', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): JsonResponse
    {
        try {
            $contact = $this->contactService->getContactById($id);
            return response()->json([
                'status' => true,
                'message' => 'Data fetched successfully!',
                'data' => $contact,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to find data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactRequest $request, $id): JsonResponse
    {
        try {
            $contact = $this->contactService->updateContact($id, $request->validated());

            return response()->json([
                'status' => true,
                'message' => ucfirst($contact->type) . ' updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
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
                'status' => true,
                'message' => ucfirst($type) . ' deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete contact: ' . $e->getMessage()
            ], 500);
        }
    }
}
