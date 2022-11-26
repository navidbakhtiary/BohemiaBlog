<?php

namespace App\Http\Responses;

use App\Classes\Creator;
use App\Classes\HttpStatus;

class BadRequestResponse extends Response
{
    public function sendPostRestorationRequired()
    {
        return $this->sendError(HttpStatus::BadRequest, Creator::createFailureMessage('post_restoration_required'));
    }

    public function sendInvalidInputs($message, $errors = [])
    {
        return $this->sendError(HttpStatus::BadRequest, $message, $errors);
    }
}
