<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\FollowUpService;
use App\Http\Requests\FollowUpRequest;
use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\DB as FacadesDB;

class LeadController extends Controller
{
    protected $service;

    public function __construct(FollowUpService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return view('leads.index');
    }


    public function getLeads(Request $request)
    {
        if ($request->ajax()) {

            $query = Contacts::select([
                'contacts.id',
                'contacts.name',
                'contacts.phone',
                'contacts.location',
                'contacts.type',
                FacadesDB::raw("CASE
                            WHEN follow_ups.id IS NOT NULL THEN 'Done'
                            ELSE 'Pending'
                        END AS follow_status"),
                'follow_ups.status as follow_up_status'
            ])
                ->leftJoin('follow_ups', 'contacts.id', '=', 'follow_ups.user_id')
                ->where('contacts.type', 'visitor');

            return DataTables::of($query)
                ->addIndexColumn()

                ->addColumn('follow_status_badge', function ($row) {
                    if ($row->follow_status == 'Done') {
                        return '<span class="badge bg-success">Done</span>';
                    }
                    return '<span class="badge bg-danger">Pending</span>';
                })

                ->addColumn('actions', function ($row) {

                    return '
                    <button class="btn btn-warning btn-sm openFollowUp" data-id="' . $row->id . '">
                        <i class="ph ph-phone-call"></i> Follow Up
                    </button>
                ';
                })

                ->rawColumns(['follow_status_badge', 'actions'])
                ->make(true);
        }
    }



    // store follow-up
    public function store(FollowUpRequest $request)
    {
        $this->service->create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Follow-up added successfully!'
        ]);
    }
}
