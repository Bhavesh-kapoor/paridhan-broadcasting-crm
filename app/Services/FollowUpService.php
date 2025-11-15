<?php

namespace App\Services;

use App\Models\FollowUp;
use Carbon\Carbon;


class FollowUpService
{
    public function create(array $data)
    {
        return FollowUp::create([
            'phone' => $data['phone'],
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
