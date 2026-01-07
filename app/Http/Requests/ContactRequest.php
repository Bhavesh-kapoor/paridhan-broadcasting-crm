<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
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
        $contactId = $this->route('contact');
        $type = $this->input('type', 'visitor');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'type' => ['required', 'in:exhibitor,visitor'],
        ];

        // Additional rules for exhibitors
        if ($type === 'exhibitor') {
            $rules = array_merge($rules, [
                'email' => ['required', 'email', 'max:255', Rule::unique('contacts')->ignore($contactId)],
                'alternate_phone' => ['nullable', 'string', 'max:20'],
                'product_type' => ['nullable', 'string', 'max:255'],
                'brand_name' => ['nullable', 'string', 'max:255'],
                'business_type' => ['nullable', 'string', 'max:255'],
                'gst_number' => ['nullable', 'string', 'max:50', Rule::unique('contacts')->ignore($contactId)],
            ]);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Contact name is required.',
            'location.required' => 'Location is required.',
            'phone.required' => 'Phone number is required.',
            'email.required' => 'Email address is required for exhibitors.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'gst_number.unique' => 'This GST number is already registered.',
            'type.in' => 'Contact type must be either exhibitor or visitor.',
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
