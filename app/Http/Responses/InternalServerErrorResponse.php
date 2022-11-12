<?php

namespace App\Http\Responses;

use App\Classes\HttpStatus;
use App\Classes\Creator;
use App\Interfaces\ErrorResponseInterface;

class InternalServerErrorResponse extends Response implements ErrorResponseInterface
{
    public function sendMessage()
    {
        return $this->sendError(HttpStatus::InternalServerError, Creator::createFailureMessage('server_error'));
    }
}
