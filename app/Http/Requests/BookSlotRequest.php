<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class BookSlotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_id' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d',
            'slots.start_time' => 'required|date_format:H:i:s',
            'slots.end_time' => 'required|date_format:H:i:s|after:slots.start_time',
            'personal_details' => 'required|array|between:1,3',
            'personal_details.*.email' => 'required|email',
            'personal_details.*.first_name' => 'required|string',
            'personal_details.*.last_name' => 'required|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => true
        ], 422));
    }
}
