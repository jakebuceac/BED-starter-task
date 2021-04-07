<?php

use App\Http\Controllers\Authentication;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
Route::POST('/register', [
    Authentication::class, 'register'
]);

Route::POST('/login', [
    Authentication::class, 'login'
]);

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/test', [
        TestController::class, 'test'
    ]);
});
