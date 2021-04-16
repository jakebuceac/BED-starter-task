<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
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
// route for the registration method in the AuthenticationController class
Route::POST('/register', [
    AuthenticationController::class, 'register'
]);

// route for the login method in the AuthenticationController class
Route::POST('/login', [
    AuthenticationController::class, 'login'
]);

Route::get('/user', [
    UserController::class, 'index'
]);

// route for the index method in the PostController class
Route::get('/post', [
    PostController::class, 'index'
]);

// route for the show method in the PostController class
Route::get('/post/{post}', [
    PostController::class, 'show'
]);

// route for the index method in the TagController class
Route::get('/tag', [
    TagController::class, 'index'
]);

// route for the show method in the TagController class
Route::get('/tag/{tag}', [
    TagController::class, 'show'
]);

// sanctum route for valid users that are given a token
Route::middleware(['auth:sanctum'])->group(function (){
    // route for the test method in the TestController class
    Route::get('/test', [
        TestController::class, 'test'
    ]);

    // route for the store method in the PostController class
    Route::post('/post', [
        PostController::class, 'store'
    ]);

    // route for the update method in the PostController class
    Route::put('/post/{post}', [
        PostController::class, 'update'
    ]);

    // route for the delete method in the PostController class
    Route::delete('/post/{post}', [
        PostController::class, 'delete'
    ]);

    // route for the store method in the TagController class
    Route::post('/tag', [
        TagController::class, 'store'
    ]);

    // route for the update method in the TagController class
    Route::put('/tag/{tag}', [
        TagController::class, 'update'
    ]);

    // route for the delete method in the TagController class
    Route::delete('/tag/{tag}', [
        TagController::class, 'delete'
    ]);

    // route for the attach method in the PostTagController class
    Route::post('/post/{post}/attach', [
        PostController::class, 'attach'
    ]);

});
