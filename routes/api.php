<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth_API\LoginController;
use App\Http\Controllers\Auth_API\RegisterController;

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
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/forgot_password', [RegisterController::class, 'forgot_password']);
Route::post('/verify_otp', [RegisterController::class, 'verify_otp']);
Route::post('/confirm_password', [RegisterController::class, 'confirm_password']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('jwt.verify')->group(function() {
     Route::get('/logout', [LoginController::class, 'logout']);
});
