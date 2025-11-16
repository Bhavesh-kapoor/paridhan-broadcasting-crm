<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
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
            'loc_name' => ['required', 'string', 'max:255'],
            'type'     => ['required', 'string', 'max:50'],
            'address'  => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB


            //  Table validation
            'tables'                => ['nullable', 'array'],
            'tables.*.table_no'     => ['required_with:tables.*.table_size,tables.*.price', 'string', 'max:100'],
            'tables.*.table_size'   => ['required_with:tables.*.table_no,tables.*.price', 'string', 'max:100'],
            'tables.*.price'        => ['required_with:tables.*.table_no,tables.*.table_size', 'numeric', 'min:0'],
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
            'loc_name.required' => 'Location name is required.',
            'loc_name.max' => 'Location name must not exceed 255 characters.',
            'type.required' => 'Type is required.',
            'type.max' => 'Type must not exceed 50 characters.',
            'address.string' => 'Address must be a valid text string.',

            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be a file of type: JPG, JPEG, or PNG.',
            'image.max'   => 'Image size must not exceed 5 MB.',

            //  Table messages
            'tables.*.table_no.required_with'   => 'Table number is required when adding table details.',
            'tables.*.table_size.required_with' => 'Table size is required when adding table details.',
            'tables.*.price.required_with'      => 'Price is required when adding table details.',
            'tables.*.price.numeric'            => 'Price must be a valid number.',
        ];
    }
}
