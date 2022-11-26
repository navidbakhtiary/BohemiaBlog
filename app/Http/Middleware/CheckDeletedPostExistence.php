<?php

namespace App\Http\Middleware;

use App\Http\Responses\NotFoundResponse;
use App\Models\Post;
use Closure;
use Illuminate\Http\Request;

class CheckDeletedPostExistence
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $deleted_post = Post::onlyTrashed()->find($request->route()->parameter('post_id'));
        if ($deleted_post) 
        {
            $request->route()->setParameter('deleted_post', $deleted_post);
            $request->route()->forgetParameter('post_id');
            return $next($request);
        }
        return (new NotFoundResponse())->sendDeletedPostNotFound();
    }
}
