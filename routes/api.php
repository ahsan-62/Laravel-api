<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users/{id?}',[UserApiController::class,'showUsers']);
Route::post('/create-user',[UserApiController::class,'createUser']);

Route::post('/create-multiuser',[UserApiController::class,'createMultiUser']);

Route::put('/update-user/{id}',[UserApiController::class,'updateUser']);

Route::delete('/delete-user/{id}',[UserApiController::class,'deleteUser']);

Route::delete('/user-delete-json',[UserApiController::class,'deleteUserJson']);

Route::delete('/delete-multiuser/{ids}',[UserApiController::class,'deleteMultiUsers']);

Route::post('/register-api-user', [UserApiController::class, 'registerApiUser']);

Route::post('/login-api-user', [UserApiController::class, 'loginApiUser']);
