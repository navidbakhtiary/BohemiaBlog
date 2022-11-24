<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Responses\InternalServerErrorResponse;
use App\Http\Responses\OkResponse;
use App\Http\Responses\UnprocessableEntityResponse;
use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function deletedIndex(Request $request)
    {
        $deleted_comments = Comment::onlyTrashed()->paginate(20);
        return (new OkResponse())->sendDeletedCommentsList($deleted_comments);
    }

    public function deletedPostIndex(Request $request)
    {
        $deleted_post = $request->route()->parameter('deleted_post');
        $deleted_comments = $deleted_post->deletedComments()->paginate(20);
        return (new OkResponse())->sendDeletedPostCommentsList($deleted_post, $deleted_comments);
    }

    public function destroy(Request $request)
    {
        try {
            $comment = $request->route()->parameter('comment');
            DB::beginTransaction();
            if ($comment->delete()) {
                DB::commit();
                return $comment->sendDeletedResponse();
            }
            DB::rollBack();
            return (new UnprocessableEntityResponse())->sendMessage();
        } catch (Exception $exc) {
            DB::rollBack();
            return (new InternalServerErrorResponse())->sendMessage();
        }
    }

    public function index(Request $request)
    {
        $post = $request->route()->parameter('post');
        $comments = $post->comments()->paginate(20);
        return (new OkResponse())->sendCommentsList($post, $comments);
    }

    public function store(CommentStoreRequest $request)
    {
        try {
            $post = $request->route()->parameter('post');
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
