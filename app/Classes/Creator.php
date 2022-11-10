<?php

namespace App\Classes;

class Creator
{
    static function createFailureMessage(string $key)
    {
        return [
            'code' => config('blog.resultcodes.failures.' . $key),
            'text' => trans('resultmessages.failures.' . $key)
        ];
    }

    static function createSuccessMessage(string $key)
    {
        return [
            'code' => config('blog.resultcodes.successes.' . $key),
            'text' => trans('resultmessages.successes.' . $key)
        ];
    }
    
    static function createValidationError(string $key, string $rule, array $specifics = null, bool $replacement = false, array $replaceables = null)
    {
        $specific_messages =
            $specifics ?
            array_map(function ($item) {
                return trans('validationmessages.messages.' . $item);
            }, $specifics) :
            [];
        $message = trans('validationmessages.rules.' . $rule) . implode(' ', $specific_messages);
        $replacers = array_merge(['attribute' => $key], ($replaceables ? $replaceables : []));
        return [
            'code' => config('blog.validationcodes.attributes.' . $key) . '-' . config('blog.validationcodes.rules.' . $rule),
            'message' => $replacement ? trans($message, $replacers) : $message
        ];
    }
}
