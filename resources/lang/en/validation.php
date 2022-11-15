<?php

use App\Classes\Creator;

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    | ALL OF LINES MOVED TO validationmessages.php
    */

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'access_token' => [
            'required' => Creator::createValidationError('access_token', 'required'),
            'string' => Creator::createValidationError('access_token', 'string'),
        ],
        'address' => [
            'max' => Creator::createValidationError('address', 'max.string'),
            'string' => Creator::createValidationError('address', 'string')
        ],
        'city' => [
            'max' => Creator::createValidationError('city', 'max.string', null, true, ['name' => 'city']),
            'regex' => Creator::createValidationError('city', 'regex', ['name_regex'], true, ['name' => 'city']),
            'string' => Creator::createValidationError('city', 'string', null, true, ['name' => 'city'])
        ],
        'content' => [
            'max' => Creator::createValidationError('content', 'max.string'),
            'required' => Creator::createValidationError('content', 'required'),
            'string' => Creator::createValidationError('content', 'string')
        ],
        'description' => [
            'max' => Creator::createValidationError('description', 'max.string'),
            'string' => Creator::createValidationError('description', 'string'),
        ],
        'device_name' => [
            'max' => Creator::createValidationError('device_name', 'max.string'),
            'required' => Creator::createValidationError('device_name', 'required'),
            'string' => Creator::createValidationError('device_name', 'string')
        ],
        'email' => [
            'email' => Creator::createValidationError('email', 'email', null, true),
            'exists' => Creator::createValidationError('email', 'exists', null, true),
            'max' => Creator::createValidationError('email', 'max.string', null, true),
            'required' => Creator::createValidationError('email', 'required', null, true),
            'string' => Creator::createValidationError('email', 'string', null, true),
            'unique' => Creator::createValidationError('email', 'unique', null, true)
        ],
        'login' => [
            'max' => Creator::createValidationError('login', 'max.string'),
            'required' => Creator::createValidationError('login', 'required'),
            'string' => Creator::createValidationError('login', 'string')
        ],
        'name' => [
            'max' => Creator::createValidationError('name', 'max.string'),
            'regex' => Creator::createValidationError('name', 'regex', ['name_regex'], true, ['name' => 'name']),
            'required' => Creator::createValidationError('name', 'required'),
            'string' => Creator::createValidationError('name', 'string')
        ],
        'nickname' => [
            'max' => Creator::createValidationError('nickname', 'max.string'),
            'regex' => Creator::createValidationError('nickname', 'regex', ['nickname_regex']),
            'string' => Creator::createValidationError('nickname', 'string')
        ],
        'paginate' => [
            'integer' => Creator::createValidationError('paginate', 'integer'),
            'min' => Creator::createValidationError('paginate', 'min.numeric')
        ],
        'password' => [
            'confirmed' => Creator::createValidationError('password', 'confirmed'),
            'max' => Creator::createValidationError('password', 'max.string'),
            'regex' => Creator::createValidationError('password', 'regex', ['password_regex']),
            'required' => Creator::createValidationError('password', 'required'),
            'string' => Creator::createValidationError('password', 'string')
        ],
        'phone' => [
            'regex' => Creator::createValidationError('phone', 'regex', ['phone_regex']),
            'required' => Creator::createValidationError('phone', 'required'),
            'string' => Creator::createValidationError('phone', 'string'),
            'unique' => Creator::createValidationError('phone', 'unique')
        ],
        'state' => [
            'max' => Creator::createValidationError('state', 'max.string', ['name_regex'], true, ['name' => 'state']),
            'regex' => Creator::createValidationError('state', 'regex', ['name_regex'], true, ['name' => 'state']),
            'string' => Creator::createValidationError('state', 'string', ['name_regex'], true, ['name' => 'state'])
        ],
        'subject' => [
            'max' => Creator::createValidationError('subject', 'max.string'),
            'required' => Creator::createValidationError('subject', 'required'),
            'string' => Creator::createValidationError('subject', 'string')
        ],
        'surname' => [
            'max' => Creator::createValidationError('surname', 'max.string'),
            'regex' => Creator::createValidationError('surname', 'regex', ['surname_regex']),
            'required' => Creator::createValidationError('surname', 'required'),
            'string' => Creator::createValidationError('surname', 'string')
        ],
        'zipcode' => [
            'digits_between' => Creator::createValidationError('zipcode', 'digits_between')
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */
    'attributes' => [
        'access_token' => 'authentication token',
        'device_name' => 'device name',
        'email' => 'email',
        'login' => 'email/phone number/username',
        'paginate' => 'number of items per page',
        'username' => 'username',
    ],

];
