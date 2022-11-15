<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Responses\InternalServerErrorResponse;
use App\Http\Responses\UnprocessableEntityResponse;
use App\Models\Admin;
use Exception;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function store(PostStoreRequest $request, Admin $admin)
    {
        try {
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
