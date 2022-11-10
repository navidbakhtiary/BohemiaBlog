<?php

namespace App\Classes;

class Creator
{
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
