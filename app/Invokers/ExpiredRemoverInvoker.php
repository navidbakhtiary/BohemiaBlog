<?php

namespace App\Invokers;

use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ExpiredRemoverInvoker
{
    function __invoke()
    {
        try
        {
            DB::beginTransaction();
            $ceil = Carbon::now()->subHours(3);
            Comment::whereHas('post', function($query) use ($ceil){
                $query->where('created_at', '<', $ceil);
            })->delete();
            Post::where('created_at', '<', $ceil)->delete();
            DB::commit();
        } catch(Exception $exc) {
            DB::rollBack();
        }
    }
}
