<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Responses\InternalServerErrorResponse;
use App\Http\Responses\OkResponse;
use App\Http\Responses\UnprocessableEntityResponse;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function destroy(Request $request)
    {
        try {
            $post = $request->route()->parameter('post');
            $comments = $post->comments;
            DB::beginTransaction();

            if ($post->delete() && ($post->comments()->delete() == count($comments))) {
                DB::commit();
                return $post->sendDeletedResponse();
            }
            DB::rollBack();
            return (new UnprocessableEntityResponse())->sendMessage();
        } catch (Exception $exc) {
            DB::rollBack();
            return (new InternalServerErrorResponse())->sendMessage();
        }
    }

    public function index()
    {
        $posts = Post::listItem()->
            withCount('comments')->
            orderByDesc('comments_count')->
            orderByDesc('updated_at')->
            paginate(20);
        if($posts->count())
        {
            return (new OkResponse())->sendPostsList($posts);
        }
        return (new OkResponse())->sendEmptyPostList();
    }
    
    public function store(PostStoreRequest $request)
    {
        try {
            $admin = $request->route()->parameter('admin');
            DB::beginTransaction();
            $post = $admin->posts()->create($request->all());
            if (!$post) 
            {
                return (new UnprocessableEntityResponse())->sendMessage();
            }
            DB::commit();
            return $post->sendCreatedResponse();
        } catch (Exception $exc) {
            DB::rollBack();
            return (new InternalServerErrorResponse())->sendMessage();
        }
    }

}
