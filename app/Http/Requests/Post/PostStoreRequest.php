<?php

namespace App\Http\Requests\Post;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class PostStoreRequest extends FormRequest
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
            'subject' => ['max:64', 'required', 'string'],
            'content' => ['max:65535', 'required', 'string']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->sendInvalidInputsResponse($validator->errors()->getMessages());
    }
}
