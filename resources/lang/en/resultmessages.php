<?php

return [

    'failures' => [
        'invalid_inputs' => 'Inputs are invalid.',
        'invalid_email_verification' => 'Email verification link is INVALID.',
        'non_existent_user' => 'The email or password is incorrect.',
        'server_error' => 'An error has occurred. Please try again in a few minutes.',
        'unauthenticated' => 'You must first log in to your account.',
        'unauthorized' => 'You cannot access this part.',
        'unprocessable' => 'It is not possible to process your request at this time. Please try again in a few minutes.',
        'user_email_verification' => 'You must verify your email address for using the account.'
    ],

    'successes' => [
        'empty_list' => 'No information is saved. The list is empty.',
        'email_verification_sent' => 'A verification link has been sent to the email.',
        'email_verified' => 'Email verified successfully. The account is activated now.',
        'saved' => 'Information was saved successfully.',
        'user_registered' => 'The user was registered successfully.',
        'user_logged_in' =>'Logging was successful. This device was saved to the user account.',
        'user_logged_out' => 'Logging out was successful.'
    ]

];
