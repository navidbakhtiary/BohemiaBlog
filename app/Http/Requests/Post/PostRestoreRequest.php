<?php

namespace App\Http\Requests\Post;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class PostRestoreRequest extends FormRequest
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
            'with_comments' => ['boolean', 'required'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->sendInvalidInputsResponse($validator->errors()->getMessages());
    }

    protected function passedValidation()
    {
        $this->with_comments = (boolean)$this->with_comments;
    }
}
