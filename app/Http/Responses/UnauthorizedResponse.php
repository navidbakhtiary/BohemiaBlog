<?php

namespace App\Http\Responses;

use App\Classes\Creator;
use App\Classes\HttpStatus;

class UnauthorizedResponse extends Response
{
    public function sendNonExistentUser()
    {
        return $this->sendError(
            HttpStatus::Unauthorized,
            Creator::createFailureMessage('non_existent_user')
        );
    }
}
