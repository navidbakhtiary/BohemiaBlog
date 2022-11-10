<?php

namespace App\Http\Responses;

use App\Classes\HttpStatus;

class BadRequestResponse extends Response
{
    public function sendInvalidInputs($message, $errors = [])
    {
        return $this->sendError(HttpStatus::BadRequest, $message, $errors);
    }
}
