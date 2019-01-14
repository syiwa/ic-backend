<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|unique:users|email|max:255',
            'phone' => 'required|digits_between:6,15|numeric',
            'address' => 'required'
        ];

        if($this->id){
            $rules = [
                'name' => 'sometimes|max:255',
                'email' => 'sometimes|nullable|unique:users,email,'.$this->id.'|email|max:255',
                'phone' => 'sometimes|digits_between:6,15|numeric',
            ];
        }

        return $rules;
    }
}
