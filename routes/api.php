<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register_student', [UserController::class, 'registerStudent']);
Route::post('login_student', [UserController::class, 'loginStudent']);
Route::post('logout_student', [UserController::class, 'logoutStudent']);

Route::get('/test', function(){
	p('working');
});

Route::post('/store', [DemoController::class, 'store']);
Route::get('/user/{flag}', [DemoController::class, 'index']);
Route::get('/show/{id}', [DemoController::class, 'show']);
Route::delete('/delete/{id}', [DemoController::class, 'destroy']);
