<?php

namespace App\Http\Responses;

use App\Classes\Creator;
use App\Classes\HttpStatus;

class NotFoundResponse extends Response
{
    public function sendPostNotFound()
    {
        return $this->sendError(
            HttpStatus::NotFound,
            Creator::createFailureMessage('post_not_found')
        );
    }

    public function sendCommentNotFound()
    {
        return $this->sendError(
            HttpStatus::NotFound,
            Creator::createFailureMessage('comment_not_found')
        );
    }
}
