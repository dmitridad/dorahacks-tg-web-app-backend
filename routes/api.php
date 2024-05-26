<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
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

Route::get('/auth/csrf', [AuthController::class, 'getCsrfToken'])
    ->name('auth.csrf');
Route::post('/auth', [AuthController::class, 'authenticate'])
    ->name('auth');
Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
})->name('auth.user');
