<?php

namespace App\Http\Responses;

use App\Classes\HttpStatus;

class OkResponse extends Response
{
    public function sendOk($message, $data = [])
    {
        return $this->sendData(HttpStatus::Ok, $message, $data);
    }
}
