<?php

namespace App\Http\Responses;

use App\Classes\Creator;
use App\Classes\HttpStatus;
use App\Http\Resources\CommentIndexResource;
use App\Http\Resources\DeletedPostIndexResource;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PostIndexResource;
use App\Http\Resources\SimplePostResource;

class OkResponse extends Response
{
    public function sendCommentsList($post, $comments)
    {
        if ($comments->count()) 
        {
            return $this->sendPaginatedData(
                HttpStatus::Ok,
                Creator::createSuccessMessage('comments_list'),
                ['post' => new SimplePostResource($post), 'comments' => CommentIndexResource::collection($comments)],
                new PaginateResource($comments)
            );
        } else {
            return $this->sendPaginatedData(HttpStatus::Ok, Creator::createSuccessMessage('empty_comments_list'));
        }
    }

    public function sendDeletedPostsList($deleted_posts)
    {
        if ($deleted_posts->count()) {
            return $this->sendPaginatedData(
                HttpStatus::Ok,
                Creator::createSuccessMessage('deleted_posts_list'),
                ['deleted posts' => DeletedPostIndexResource::collection($deleted_posts)],
                new PaginateResource($deleted_posts)
            );
        } else {
            return $this->sendPaginatedData(HttpStatus::Ok, Creator::createSuccessMessage('empty_deleted_posts_list'));
        }
    }

    public function sendEmptyCommentsList()
    {
        return $this->sendData(HttpStatus::Ok, Creator::createSuccessMessage('empty_comments_list'));
    }

    public function sendOk($message, $data = [])
    {
        return $this->sendData(HttpStatus::Ok, $message, $data);
    }

    public function sendPostsList($posts)
    {
        if ($posts->count())
        {
            return $this->sendPaginatedData(
                HttpStatus::Ok, 
                Creator::createSuccessMessage('posts_list'), 
                ['posts' => PostIndexResource::collection($posts)], 
                new PaginateResource($posts)
            );
        } else {
            return $this->sendPaginatedData(HttpStatus::Ok, Creator::createSuccessMessage('empty_posts_list'));
        }
    }
}
