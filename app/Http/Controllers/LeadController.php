<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\FollowUpService;
use App\Http\Requests\FollowUpRequest;
use App\Models\FollowUp;
use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\DB as FacadesDB;
use Carbon\Carbon;


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


            $latestFollowUps = FacadesDB::table('follow_ups')
                ->select('phone', FacadesDB::raw('MAX(created_at) as max_created_at'))
                ->groupBy('phone');

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
                'follow_ups.status as follow_up_status',
                'follow_ups.created_at as follow_up_date'
            ])
                ->leftJoinSub($latestFollowUps, 'latest_followup', function ($join) {
                    $join->on('contacts.phone', '=', 'latest_followup.phone');
                })
                ->leftJoin('follow_ups', function ($join) {
                    $join->on('contacts.phone', '=', 'follow_ups.phone')
                        ->on('follow_ups.created_at', '=', 'latest_followup.max_created_at');
                })
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
                    <button class="btn btn-secondary btn-sm openFollowUp" data-phone="' . $row->phone . '">
                        <i class="ph ph-plus"></i>Add
                    </button>
                    <button class="btn btn-warning btn-sm viewFollowUp ms-2" data-phone="' . $row->phone . '">
                        <i class="ph ph-eyes"></i> View
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



    public function getFollowUps($phone, FollowUpService $service)
    {
        $data = $service->getFollowUps($phone);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
