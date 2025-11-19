<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'type' => ['required', 'in:email,sms,whatsapp'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            // 'recipients' => ['required', 'array', 'min:1'],
            // 'recipients.*' => ['required', 'string', 'exists:contacts,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Campaign name is required.',
            'subject.required' => 'Campaign subject is required.',
            'message.required' => 'Campaign message is required.',
            'type.required' => 'Campaign type is required.',
            'type.in' => 'Campaign type must be email, sms, or whatsapp.',
            'scheduled_at.after' => 'Scheduled date must be in the future.',
            'recipients.required' => 'Please select at least one recipient.',
            'recipients.min' => 'Please select at least one recipient.',
            'recipients.*.exists' => 'One or more selected recipients are invalid.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        // Throw JSON response instead of HTML redirect
        throw new HttpResponseException(response()->json([
            'status' => 'validation_error',
            'message' => $validator->errors()->all(),
        ]));
    }
}
