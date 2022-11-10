<?php

return [

    'failures' => [
        'invalid_inputs' => 'E091',
        'invalid_email_verification' => 'E092',
        'non_existent_user' => 'E141',
        'server_error' => 'E191',
        'unauthenticated' => 'E211',
        'unauthorized' => 'E212',
        'unprocessable' => 'E213',
        'user_email_verification' => 'E214'
    ],

    'successes' => [
        'empty_list' => 'S051',
        'email_verification_sent' => 'S059',
        'email_verified' => 'S0510',
        'saved' => 'S191',
        'user_registered' => 'S211',
        'user_logged_in' => 'S212',
        'user_logged_out' => 'S213'
    ]

];
