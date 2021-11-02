<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\UserController;
use App\Models\Blog;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::get('/list', [BlogController::class, 'get']);      /*  single and all blog without passport */
Route::get('/blog/{id}', [BlogController::class, 'get_single']);      /*  single and all blog without passport */


Route::group(['middleware' => 'auth:api'], function(){

        // Route::post('/post_image', [BlogController::class, 'post_image']);
        Route::post('/post', [BlogController::class, 'create']);
        Route::post('/edit/{id}', [BlogController::class, 'update']);
        Route::delete('/delete/{id}', [BlogController::class, 'delete']);
        Route::post('/status/{id}', [BlogController::class, 'update_status']);
        Route::delete('/logout', [UserController::class, 'logout']);
        Route::delete('/logout_all', [UserController::class, 'hardLogout']);
    });

