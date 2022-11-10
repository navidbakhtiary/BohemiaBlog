<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Schema\Builder;

class DefaultStringMaxLengthRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $attribute;
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        if(is_string($value) && strlen($value) <= Builder::$defaultStringLength)
        {
            return true;
        }
        return is_null($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return [trans('validation.custom.' . $this->attribute . '.max', ['max' => Builder::$defaultStringLength])];
    }
}
