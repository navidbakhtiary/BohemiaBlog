<?php

namespace App\Traits;

use App\Classes\Creator;
use App\Http\Responses\BadRequestResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ResponseTrait
{
    public function sendInvalidInputsResponse(array $errors)
    {
        throw new HttpResponseException((new BadRequestResponse())->sendInvalidInputs(Creator::createFailureMessage('invalid_inputs'), $errors));
    }  
}
