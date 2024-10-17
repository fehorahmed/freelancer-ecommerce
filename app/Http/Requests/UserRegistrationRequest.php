<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
       // $errors = $validator->errors()->toArray();

        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'father_name' => 'required|string|max:255',
            'job_type' => 'required|string|max:255',
            'nid' => 'required|string|max:255',
            'nid_image' => 'nullable|image|mimes:jpeg,jpg,png|max:1024',
            'present_division_id' => 'required|numeric',
            'present_district_id' => 'required|numeric',
            'present_sub_district_id' => 'required|numeric',
            'present_address' => 'required|string|max:255',

            'permanent_division_id' => 'required|numeric',
            'permanent_district_id' => 'required|numeric',
            'permanent_sub_district_id' => 'required|numeric',
            'permanent_address' => 'required|string|max:255',

            'office_division_id' => 'nullable|required_if:job_type,GOVT|numeric',
            'office_district_id' => 'nullable|required_if:job_type,GOVT|numeric',
            'office_sub_district_id' => 'nullable|required_if:job_type,GOVT|numeric',
            'office_address' => 'nullable|required_if:job_type,GOVT|string|max:255',

        ];
    }
}
