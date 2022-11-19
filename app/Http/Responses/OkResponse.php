<?php

namespace App\Http\Responses;

use App\Classes\Creator;
use App\Classes\HttpStatus;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PostIndexResource;

class OkResponse extends Response
{
    public function sendEmptyPostList()
    {
        return $this->sendData(HttpStatus::Ok, Creator::createSuccessMessage('empty_posts_list'));
    }

    public function sendOk($message, $data = [])
    {
        return $this->sendData(HttpStatus::Ok, $message, $data);
    }

    public function sendPostsList($posts)
    {
        return $this->sendData(
            HttpStatus::Ok, 
            Creator::createSuccessMessage('posts_list'), 
            ['posts' => PostIndexResource::collection($posts)], 
            new PaginateResource($posts)
        );
    }
}
