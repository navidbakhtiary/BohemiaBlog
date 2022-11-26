<?php

namespace App\Http\Responses;

use App\Classes\Creator;
use App\Classes\HttpStatus;
use App\Interfaces\ErrorResponseInterface;

class UnprocessableEntityResponse extends Response implements ErrorResponseInterface
{
    public function sendMessage()
    {
        return $this->sendError(HttpStatus::UnprocessableEntity, Creator::createFailureMessage('unprocessable'));
    }
}
