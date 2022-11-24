<?php

namespace App\Http\Responses;

use App\Classes\Creator;
use App\Classes\HttpStatus;
use App\Http\Resources\CommentIndexResource;
use App\Http\Resources\DeletedCommentIndexResource;
use App\Http\Resources\DeletedPostIndexResource;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PostIndexResource;
use App\Http\Resources\SimpleDeletedPostResource;
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

    public function sendDeletedCommentsList($deleted_comments)
    {
        if ($deleted_comments->count()) {
            return $this->sendPaginatedData(
                HttpStatus::Ok,
                Creator::createSuccessMessage('deleted_comments_list'),
                ['deleted comments' => DeletedCommentIndexResource::collection($deleted_comments)],
                new PaginateResource($deleted_comments)
            );
        } else {
            return $this->sendPaginatedData(HttpStatus::Ok, Creator::createSuccessMessage('empty_deleted_comments_list'));
        }
    }

    public function sendDeletedPostCommentsList($deleted_post, $deleted_comments)
    {
        if ($deleted_comments->count()) {
            return $this->sendPaginatedData(
                HttpStatus::Ok,
                Creator::createSuccessMessage('deleted_post_comments_list'),
                ['deleted post' => new SimpleDeletedPostResource($deleted_post), 'comments' => CommentIndexResource::collection($deleted_comments)],
                new PaginateResource($deleted_comments)
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
