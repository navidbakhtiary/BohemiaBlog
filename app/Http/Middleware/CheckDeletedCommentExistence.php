<?php

namespace App\Http\Middleware;

use App\Http\Responses\NotFoundResponse;
use App\Models\Comment;
use Closure;
use Illuminate\Http\Request;

class CheckDeletedCommentExistence
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
        $deleted_comment = Comment::onlyTrashed()->find($request->route()->parameter('comment_id'));
        if ($deleted_comment) 
        {
            $request->route()->setParameter('deleted_comment', $deleted_comment);
            $request->route()->forgetParameter('comment_id');
            return $next($request);
        }
        return (new NotFoundResponse())->sendDeletedCommentNotFound();
    }
}
