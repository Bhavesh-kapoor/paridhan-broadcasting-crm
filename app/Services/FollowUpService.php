<?php

namespace App\Services;

use App\Models\FollowUp;
use Exception;
use Illuminate\Support\Facades\DB;


class FollowUpService
{
    public function create(array $data)
    {
        return FollowUp::create([
            'user_id' => $data['user_id'],
            'status' => $data['status'],
            'comment' => $data['comment'],
            'next_followup_date' => $data['next_followup_date'] ?? null,
            'next_followup_time' => $data['next_followup_time'] ?? null,
            'employee_id' => auth()->id(),
        ]);
    }
}
