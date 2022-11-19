<?php

namespace App\Http\Responses;

class Response
{
    public function sendData($status, $message = null, $data = [], $pagination = null)
    {
        return response(['message' => $message, 'data' => $data, 'pagination' => $pagination], $status)
            ->header('Content-Type', 'application/json');
    }

    public function sendError($status, $message = null, $errors = [])
    {
        return response(['message' => $message, 'errors' => $errors], $status)
            ->header('Content-Type', 'application/json');
    }
}
