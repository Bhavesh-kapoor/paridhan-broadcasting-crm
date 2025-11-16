<?php

namespace App\Services;

use App\Models\FollowUp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;


class FollowUpService
{
    public function getFollowUpData($phone)
    {
        // ===========================
        // 1. Get Contact + Latest Follow-up
        // ===========================
        $latestFollowup = FollowUp::query()
            ->leftJoin('contacts', 'contacts.phone', '=', 'follow_ups.phone')
            ->select(
                'follow_ups.*',
                'contacts.name as contact_name',
                'contacts.phone as contact_phone'
            )
            ->where('follow_ups.phone', $phone)
            ->orderByDesc('follow_ups.created_at')
            ->firstOrFail();

        // ===========================
        // 2. Get Follow-up History
        // ===========================
        $history = $this->getFollowUps($phone);

        // Return both
        return [
            'contact' => [
                'name' => $latestFollowup->contact_name,
                'phone' => $latestFollowup->contact_phone,
            ],
            'latest_followup' => $latestFollowup,
            'history' => $history
        ];
    }


    public function getAllLeadsList()
    {
        // Step 1: Get the latest follow_up timestamp for each phone
        $latestFollowUps = DB::table('follow_ups')
            ->select('phone', DB::raw('MAX(created_at) AS max_created_at'))
            ->groupBy('phone');

        // Step 2: Main Query with LEFT JOIN SUBQUERY
        $query = DB::table('contacts')
            ->select([
                'contacts.id',
                'contacts.name',
                'contacts.phone',
                'contacts.location',
                'contacts.type',

                DB::raw("CASE
                        WHEN follow_ups.id IS NOT NULL THEN 'Done'
                        ELSE 'Pending'
                    END AS follow_status"),

                'follow_ups.status AS follow_up_status',
                'follow_ups.created_at AS follow_up_date',
            ])
            ->leftJoinSub($latestFollowUps, 'latest_followup', function ($join) {
                $join->on('contacts.phone', '=', 'latest_followup.phone');
            })
            ->leftJoin('follow_ups', function ($join) {
                $join->on('contacts.phone', '=', 'follow_ups.phone')
                    ->on('follow_ups.created_at', '=', 'latest_followup.max_created_at');
            })
            // ->where('contacts.type', 'visitor')
            ->orderByDesc('contacts.id');


        // Step 3: send to DataTables
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($lead) {

                $phone = $lead->phone;

                return '
                <button class="btn btn-primary btn-sm addBtn"
                        data-phone="' . $phone . '"
                        data-bs-toggle="tooltip"
                        data-bs-placement="left"
                        title="Add Follow Up">
                    <i class="bx bx-plus"></i>
                </button>
                <button class="btn btn-info btn-sm ViewBtn"
                        data-phone="' . $phone . '"  editRoute="' . route('leads.edit', $phone) . '"
                        data-bs-toggle="tooltip"
                        data-bs-placement="left"
                        title="Delete Lead">
                    <i class="bx bx-show"></i>
                </button>
            ';
            })
            ->addColumn('follow_status_badge', function ($row) {
                if ($row->follow_status == 'Done') {
                    return '<span class="badge bg-success">Done</span>';
                }
                return '<span class="badge bg-danger">Pending</span>';
            })
            ->rawColumns(['action', 'follow_status_badge'])
            ->make(true);
    }



    public function create(array $data)
    {
        return FollowUp::create([
            'phone' => $data['hidden_id'],
            'status' => $data['status'],
            'comment' => $data['comment'],
            'next_followup_date' => $data['next_followup_date'] ?? null,
            'next_followup_time' => $data['next_followup_time'] ?? null,
            'employee_id' => auth()->id(),
        ]);
    }


    public function getFollowUps($phone)
    {
        $data = FollowUp::where('follow_ups.phone', $phone)
            ->leftJoin('users', 'follow_ups.employee_id', '=', 'users.id')
            ->select(
                'follow_ups.status',
                'follow_ups.comment',
                'follow_ups.next_followup_date',
                'follow_ups.next_followup_time',
                'follow_ups.created_at',
                'users.name as users_name'
            )
            ->orderBy('follow_ups.created_at', 'DESC')
            ->get()
            ->map(function ($item) {

                $date = Carbon::parse($item->created_at);

                $item->formatted_date = $date->format('d-m-y');  // dd-mm-yy
                $item->formatted_time = $date->format('h:i A');  // hh:mm AM/PM

                return $item;
            });

        return $data;
    }
}
