<?php

namespace App\Http\Middleware;

use App\Http\Responses\ForbiddenResponse;
use App\Http\Responses\NotFoundResponse;
use App\Models\Post;
use Closure;
use Illuminate\Http\Request;

class CheckPostExistence
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
        $post = Post::find($request->route()->parameter('post_id'));
        if ($post) 
        {
            $request->route()->setParameter('post', $post);
            $request->route()->forgetParameter('post_id');
            return $next($request);
        }
        return (new NotFoundResponse())->sendPostNotFound();
    }
}
