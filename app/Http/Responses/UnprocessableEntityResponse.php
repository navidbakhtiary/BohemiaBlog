<?php

namespace App\Http\Responses;

use App\Classes\HttpStatus;
use App\Http\Classes\Creator;
use App\Interfaces\ErrorResponseInterface;

class UnprocessableEntityResponse extends Response implements ErrorResponseInterface
{
    public function sendMessage()
    {
        return $this->sendError(HttpStatus::UnprocessableEntity, Creator::createFailureMessage('unprocessable'));
    }
}
