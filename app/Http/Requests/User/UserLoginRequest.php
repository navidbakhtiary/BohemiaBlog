<?php

namespace App\Http\Requests\User;

use App\Traits\ResponseTrait;
use App\Rules\DefaultStringMaxLengthRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserLoginRequest extends FormRequest
{
    use ResponseTrait;
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
            'login' => [new DefaultStringMaxLengthRule(), 'required', 'string'],
            'password' => [new DefaultStringMaxLengthRule(), 'required', 'string'],
            'device_name' => [new DefaultStringMaxLengthRule(), 'required', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->sendInvalidInputsResponse($validator->errors()->getMessages());
    }
}
