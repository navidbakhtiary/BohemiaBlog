<?php

namespace App\Http\Responses;

class Response
{
    public function sendData($status, $message = null, $data = [])
    {
        return response(['message' => $message, 'data' => $data], $status)
            ->header('Content-Type', 'application/json');
    }

    public function sendError($status, $message = null, $errors = [])
    {
        return response(['message' => $message, 'errors' => $errors], $status)
            ->header('Content-Type', 'application/json');
    }
}
