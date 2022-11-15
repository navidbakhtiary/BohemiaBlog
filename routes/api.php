<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckUserIsAdmin;
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
    }
);

//---------------------------------- POST endpoints
Route::middleware(['auth:sanctum', CheckUserIsAdmin::class])->group(function () 
    {
        Route::prefix('post')->group(function () 
            {
                Route::post('save', [PostController::class, 'store']);
            }
        );
    }
);