<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\StrongPassword;
use App\Rules\ValidMobileNumber;

class UserFormRequest extends FormRequest
{
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'=>false,
                'errors'=> $validator->errors()
            ],200)
        );
    }


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
        if(request()->update_id){
            $rules['email'][2] = 'unique:users,email,'.request()->update_id;
            $rules['mobile_no'][1] = 'unique:users,mobile_no,'.request()->update_id;
        }else{
            $rules['password']              = ['required','string','min:6', 'confirmed',new StrongPassword];
            $rules['password_confirmation'] = ['required','string'];
        }
        $rules['mobile_no'][2] = new ValidMobileNumber;
        return $rules;
        
    }
}
