<?php

return [

    'rules' => [
        'accepted' => 'The :attribute must be accepted.', //01
        'active_url' => 'The :attribute is not a valid URL.', //02
        'after' => 'The :attribute must be a date after :date.', //03
        'after_or_equal' => 'The :attribute must be a date after or equal to :date.', //04
        'alpha' => 'The :attribute must only contain letters.', //05
        'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.', //06
        'alpha_num' => 'The :attribute must only contain letters and numbers.', //07
        'array' => 'The :attribute must be an array.', //08
        'audio' => 'The :attribute field must be an audio/sound file.', //97
        'before' => 'The :attribute must be a date before :date.', //09
        'before_or_equal' => 'The :attribute must be a date before or equal to :date.', //10
        'between' => [
            'numeric' => 'The :attribute must be between :min and :max.', //11
            'file' => 'The :attribute must be between :min and :max kilobytes.', //12
            'string' => 'The :attribute must be between :min and :max characters.', //13
            'array' => 'The :attribute must have between :min and :max items.', //14
        ],
        'boolean' => 'The :attribute field must be true or false.', //15
        'confirmed' => 'The :attribute confirmation does not match.', //16
        'current_password' => 'The password is incorrect.', //17
        'date' => 'The :attribute is not a valid date.', //18
        'date_equals' => 'The :attribute must be a date equal to :date.', //19
        'date_format' => 'The :attribute does not match the format :format.', //20
        'different' => 'The :attribute and :other must be different.', //21
        'digits' => 'The :attribute must be :digits digits.', //22
        'digits_between' => 'The :attribute must be between :min and :max digits.', //23
        'dimensions' => 'The :attribute has invalid image dimensions.', //24
        'distinct' => 'The :attribute field has a duplicate value.', //25
        'document' => 'The :attribute field must be a document file.', //98
        'email' => 'The :attribute must be a valid email address.', //26
        'ends_with' => 'The :attribute must end with one of the following: :values.', //27
        'exists' => 'The selected :attribute does not exist.', //28
        'file' => 'The :attribute must be a file.', //29
        'filled' => 'The :attribute field must have a value.', //30
        'gt' => [
            'numeric' => 'The :attribute must be greater than :value.', //31
            'file' => 'The :attribute must be greater than :value kilobytes.', //32
            'string' => 'The :attribute must be greater than :value characters.', //33
            'array' => 'The :attribute must have more than :value items.', //34
        ],
        'gte' => [
            'numeric' => 'The :attribute must be greater than or equal :value.', //35
            'file' => 'The :attribute must be greater than or equal :value kilobytes.', //36
            'string' => 'The :attribute must be greater than or equal :value characters.', //37
            'array' => 'The :attribute must have :value items or more.', //38
        ],
        'image' => 'The :attribute field must be an image file.', //39
        'in' => 'The selected :attribute is invalid.', //40
        'in_array' => 'The :attribute field does not exist in :other.', //41
        'integer' => 'The :attribute must be an integer.', //42
        'ip' => 'The :attribute must be a valid IP address.', //43
        'ipv4' => 'The :attribute must be a valid IPv4 address.', //44
        'ipv6' => 'The :attribute must be a valid IPv6 address.', //45
        'json' => 'The :attribute must be a valid JSON string.', //46
        'lt' => [
            'numeric' => 'The :attribute must be less than :value.', //47
            'file' => 'The :attribute must be less than :value kilobytes.', //48
            'string' => 'The :attribute must be less than :value characters.', //49
            'array' => 'The :attribute must have less than :value items.', //50
        ],
        'lte' => [
            'numeric' => 'The :attribute must be less than or equal :value.', //51
            'file' => 'The :attribute must be less than or equal :value kilobytes.', //52
            'string' => 'The :attribute must be less than or equal :value characters.', //53
            'array' => 'The :attribute must not have more than :value items.', //54
        ],
        'max' => [
            'numeric' => 'The :attribute must not be greater than :max.', //55
            'file' => 'The :attribute size must not be larger than :max kilobytes.', //56
            'string' => 'The :attribute must not have more than :max characters.', //57
            'array' => 'The :attribute must not have more than :max items.', //58
        ],
        'mimes' => 'The :attribute must be a file of type: :values.', //59
        'mimetypes' => 'The :attribute must be a file of type: :values.', //60
        'min' => [
            'numeric' => 'The :attribute must be at least :min.', //61
            'file' => 'The :attribute must be at least :min kilobytes.', //62
            'string' => 'The :attribute must be at least :min characters.', //63
            'array' => 'The :attribute must have at least :min items.', //64
        ],
        'multiple_of' => 'The :attribute must be a multiple of :value.', //65
        'not_in' => 'The selected :attribute is invalid.', //66
        'not_regex' => 'The :attribute format is invalid.', //67
        'numeric' => 'The :attribute must be a number.', //68
        'password' => 'The password is incorrect.', //69
        'present' => 'The :attribute field must be present.', //70
        'regex' => 'The :attribute format is invalid.', //71
        'required' => 'The :attribute field is required.', //72
        'required_if' => 'The :attribute field is required when :other is :value.', //73
        'required_unless' => 'The :attribute field is required unless :other is in :values.', //74
        'required_with' => 'The :attribute field is required when :values is present.', //75
        'required_with_all' => 'The :attribute field is required when :values are present.', //76
        'required_without' => 'The :attribute field is required when :values is not present.', //77
        'required_without_all' => 'The :attribute field is required when none of :values are present.', //78
        'prohibited' => 'The :attribute field is prohibited.', //79
        'prohibited_if' => 'The :attribute field is prohibited when :other is :value.', //80
        'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.', //81
        'same' => 'The :attribute and :other must match.', //82
        'size' => [
            'numeric' => 'The :attribute must be :size.', //83
            'file' => 'The :attribute must be :size kilobytes.', //84
            'string' => 'The :attribute must be :size characters.', //85
            'array' => 'The :attribute must contain :size items.', //86
        ],
        'starts_with' => 'The :attribute must start with one of the following: :values.', //87
        'string' => 'The :attribute must be a string.', //88
        'timezone' => 'The :attribute must be a valid timezone.', //89
        'unique' => 'The :attribute has already been taken.', //90
        'uploaded' => 'The :attribute failed to upload.', //91
        'url' => 'The :attribute must be a valid URL.', //92
        'uuid' => 'The :attribute must be a valid UUID.', //93
        'required_search' => 'For search, the :attribute must have value.', //72 (code is the same as required code)
        'used' => 'This :attribute has already been registered by you.', //94
        'in_month' => 'The selected :attribute must be a valid UTC timestamp in current or next month.',//95
        'after_timestamp' => 'The selected :attribute must be a valid UTC timestamp after :start_timestamp.', //96
        'array_max_saved' => ':saved :attribute stored before. Count of stored :attribute can not be more than :max item.', //97'
        'array_existence' => 'Some of the :attribute do not exist.', //98
        'integer_id' => 'The :attribute must be a valid integer ID.' //99
    ],

    'messages' => [
        'name_regex' => 'The :name can contain only letters and space.',
        'nickname_regex' => 'The nickname can contain only letters, numbers and space.',
        'password_regex' => 'The password must contain at least one uppercase letter, one lowercase letter and one number. The length must between 8 to 20 characters. Also using special characters(@#$&) is optional.',
        'phone_regex' => 'The phone number must be in the format +########## that contains the country code. There must be at least 10 digits in the phone number.',
        'surname_regex' => 'The surname can contain only letters and space.',
    ]

];
