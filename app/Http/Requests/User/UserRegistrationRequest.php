<?php

namespace App\Http\Requests\User;

use App\Traits\ResponseTrait;
use App\Rules\DefaultStringMaxLengthRule;
use App\Rules\NameRule;
use App\Rules\NicknameRule;
use App\Rules\PasswordRule;
use App\Rules\PersonNameRule;
use App\Rules\PhoneNumberRule;
use App\Rules\UniqueEmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserRegistrationRequest extends FormRequest
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
            'name' => [new DefaultStringMaxLengthRule(), new PersonNameRule(), 'required', 'string'],
            'surname' => [new DefaultStringMaxLengthRule(), new PersonNameRule(), 'required', 'string'],
            'nickname' => [new DefaultStringMaxLengthRule(), new NicknameRule(), 'nullable', 'string'],
            'email' => ['email:dns,spoof,filter', new DefaultStringMaxLengthRule(), 'required', 'string', new UniqueEmailRule()],
            'phone' => [new PhoneNumberRule(), 'required', 'string', 'unique:users'],
            'address' => [new DefaultStringMaxLengthRule(), 'nullable', 'string'],
            'city' => [new DefaultStringMaxLengthRule(), new NameRule(), 'nullable', 'string'],
            'state' => [new DefaultStringMaxLengthRule(), new NameRule(), 'nullable', 'string'],
            'zipcode' => ['digits_between:5,10', 'nullable'],
            'password' => ['required', 'string', new PasswordRule()],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->sendInvalidInputsResponse($validator->errors()->getMessages());
    }
}
