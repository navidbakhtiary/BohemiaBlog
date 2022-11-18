<?php

namespace App\Http\Middleware;

use App\Http\Responses\NotFoundResponse;
use Closure;
use Illuminate\Http\Request;

class CheckPostCommentExistence
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
        $comment = $request->route()->parameter('post')->comments()->find($request->route()->parameter('comment_id'));
        if ($comment) {
            $request->route()->setParameter('comment', $comment);
            $request->route()->forgetParameter('comment_id');
            return $next($request);
        }
        return (new NotFoundResponse())->sendCommentNotFound();
    }
}
