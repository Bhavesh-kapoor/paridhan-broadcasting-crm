<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'phone',
        'status',
        'next_followup_date',
        'next_followup_time',
        'comment',
        'employee_id'
    ];
}
