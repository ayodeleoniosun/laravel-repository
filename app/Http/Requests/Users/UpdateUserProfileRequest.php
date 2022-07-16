<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'phone_number' => 'required|string|min:10|max:15',
            'state'        => 'required|string|exists:states,id',
            'city'         => 'required|string|exists:cities,id',
        ];
    }
}
