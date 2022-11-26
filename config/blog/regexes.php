<?php

return [
    'name' => '/^[a-zA-Z\x{0020}]*$/',
    'nickname' => '/^[a-zA-Z0-9\x{0020}]*$/',
    'password' => '/^(?=[0-9a-zA-Z#@\$&]+$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,20}/',
    'person_name' => '/^[a-zA-Z\x{0020}]+$/',
    'phone_number' => '/^\+[1-9]\d{9,15}$/'
];
