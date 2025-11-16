<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FollowUpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'hidden_id' => 'required',
            'status' => 'required|in:busy,interested,materialised',
            'comment' => 'required|string|max:1000',

            // Busy
            'next_followup_date' => 'required_if:status,busy|nullable|date',
            'next_followup_time' => 'required_if:status,busy|nullable',

            // Materialised
            'booking_date'     => 'required_if:status,materialised|nullable|date',
            'booking_location' => 'required_if:status,materialised|nullable|string',
            'table_no'         => 'required_if:status,materialised|nullable|string',
            'price'            => 'required_if:status,materialised|nullable|numeric',
            'amount_paid'      => 'required_if:status,materialised|nullable|numeric',
        ];
    }
}
