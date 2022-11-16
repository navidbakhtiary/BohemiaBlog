<?php

namespace App\Http\Requests\Comment;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CommentStoreRequest extends FormRequest
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
            'content' => ['max:500', 'required', 'string']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->sendInvalidInputsResponse($validator->errors()->getMessages());
    }
}
