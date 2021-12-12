<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
class UserFormRequest extends FormRequest
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
       
            $rules = User::VALIDATION_RULES;
            if (request()->update_id) {
                $rules['email'][2] = 'unique:users,email,'.request()->update_id;
                $rules['mobile_no'][2] = 'unique:users,mobile_no,'.request()->update_id;
            }
            return $rules;
        
    }
}
