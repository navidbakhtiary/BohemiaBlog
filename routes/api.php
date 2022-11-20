<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckPostExistence;
use App\Http\Middleware\CheckUserIsAdmin;
use App\Http\Middleware\CheckPostCommentExistence;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//---------------------------------- USER endpoints
Route::prefix('user')->group(function () 
{
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
});

//---------------------------------- POST endpoints
Route::middleware(['auth:sanctum', CheckUserIsAdmin::class])->group(function () 
{
    Route::prefix('post')->group(function () 
    {
        Route::get('list', [PostController::class, 'index'])->withoutMiddleware(['auth:sanctum', CheckUserIsAdmin::class]);
        Route::post('save', [PostController::class, 'store']);
        Route::middleware(CheckPostExistence::class)->
            prefix('{post_id}')->group(function ()
            {
                Route::get('/', [PostController::class, 'show'])->withoutMiddleware(['auth:sanctum', CheckUserIsAdmin::class]);
                Route::prefix('comment')->group(function ()
                {
                    Route::get('/list', [CommentController::class, 'index'])->withoutMiddleware(['auth:sanctum', CheckUserIsAdmin::class])->name('post.comments');
                    Route::post('save', [CommentController::class, 'store'])->withoutMiddleware(CheckUserIsAdmin::class);
                    Route::middleware(CheckPostCommentExistence::class)->
                        prefix('{comment_id}')->group(function () 
                        {
                            Route::post('delete', [CommentController::class, 'destroy']);
                        });   
                });
                Route::post('delete', [PostController::class, 'destroy']);
            }); 
    });
});