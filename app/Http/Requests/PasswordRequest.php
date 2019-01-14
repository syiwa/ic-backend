<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
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
            'old_password' =>  'required|min:6|max:16',
            'new_password' =>  'required|min:6|max:16',
            'new_password_retype' =>  'required|min:6|max:16|same:new_password'
        ];
    }
}
