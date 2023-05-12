<?php

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

/**
 * TEST
 */
Route::group(['prefix' => 'ping'], function () {
    Route::get('/', function () {
        $git_version = trim(exec('git rev-parse --short=6 HEAD'));
        return response()->json([
            'time' => date('Y-m-d H:i:s'), 'env' => env('APP_ENV'),
            'git_version' => $git_version,
        ]);
    });
});

/**
 * POSTS
 */
Route::group(['prefix' => 'post'], function () {
    Route::post('/', 'PostsController@store');
});

/**
 * COMMENTS
 */
Route::group(['prefix' => 'comment'], function () {
    Route::post('/', 'CommentsController@store');
});

