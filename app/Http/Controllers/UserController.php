<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegistrationRequest;
use App\Http\Responses\InternalServerErrorResponse;
use App\Http\Responses\UnauthorizedResponse;
use App\Http\Responses\UnprocessableEntityResponse;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(UserLoginRequest $request)
    {
        $user = User::where('email', $request->login)->
            orWhere('phone', $request->login)->
            orWhere('username', $request->login)->
            first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return (new UnauthorizedResponse())->sendNonExistentUser();
        }
        try {
            DB::beginTransaction();
            $user->tokens()->where('name', $request->device_name)->delete();
            $token = $user->createToken($request->device_name);
            if (!$token) {
                DB::rollBack();
                return (new UnprocessableEntityResponse())->sendMessage();
            }
            DB::commit();
            return $user->sendTokenResponse($token);
        } catch (Exception $exc) {
            DB::rollBack();
            return (new InternalServerErrorResponse())->sendMessage();
        }
    }
    
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
