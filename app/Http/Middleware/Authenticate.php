<?php

namespace App\Http\Middleware;

use App\Http\Responses\UnauthorizedResponse;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function unauthenticated($request, array $guards)
    {
        abort((new UnauthorizedResponse())->sendUnauthenticated());
    }
}
