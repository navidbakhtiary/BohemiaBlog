<?php

namespace App\Http\Responses;

use App\Classes\HttpStatus;

class CreatedResponse extends Response
{
    public function sendCreated($message, $data = [])
    {
        return $this->sendData(HttpStatus::Created, $message, $data);
    }
}
