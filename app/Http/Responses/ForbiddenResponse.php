<?php

namespace App\Http\Responses;

use App\Classes\Creator;
use App\Classes\HttpStatus;

class ForbiddenResponse extends Response
{
    public function sendUnauthorizedMessage()
    {
        return $this->sendError(
            HttpStatus::Forbidden,
            Creator::createFailureMessage('unauthorized')
        );
    }
}
