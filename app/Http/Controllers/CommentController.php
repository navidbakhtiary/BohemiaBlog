<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Responses\InternalServerErrorResponse;
use App\Http\Responses\UnprocessableEntityResponse;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function store(CommentStoreRequest $request, Post $post)
    {
        try {
            $user = Auth::user();
            DB::beginTransaction();
            $comment = $user->comments()->create(['post_id' => $post->id, 'content' => $request->content]);
            if (!$comment) {
                return (new UnprocessableEntityResponse())->sendMessage();
            }
            DB::commit();
            return $comment->sendCreatedResponse();
        } catch (Exception $exc) {
            DB::rollBack();
            return (new InternalServerErrorResponse())->sendMessage();
        }
    }
}
