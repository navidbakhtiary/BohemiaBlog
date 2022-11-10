<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRegistrationRequest;
use App\Http\Responses\CreatedResponse;
use App\Http\Responses\InternalServerErrorResponse;
use App\Http\Responses\UnprocessableEntityResponse;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function register(UserRegistrationRequest $request)
    {
        try
        {
            $user = new User($request->all());
            $user->save();
            if (!$user)
            {
                return (new UnprocessableEntityResponse())->sendMessage();
            }
            return $user->sendCreatedResponse();
        } catch (Exception $exc) {
            return (new InternalServerErrorResponse())->sendMessage();
        }
    }
}
