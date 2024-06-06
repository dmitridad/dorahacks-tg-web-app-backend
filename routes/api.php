<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
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
Route::prefix('auth')->group(function () {
    Route::post('/', [AuthController::class, 'authenticate'])->name('auth');
    Route::get('/csrf', [AuthController::class, 'getCsrfToken'])->name('auth.csrf');
    Route::middleware(['auth:sanctum'])
        ->get('/user', [AuthController::class, 'user'])
        ->name('auth.user');
});

Route::middleware('auth')
    ->post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::prefix('rooms')->group(function () {
    Route::middleware(['auth:sanctum'])
        ->get('/', [RoomController::class, 'index'])
        ->name('rooms.index');
    Route::middleware(['auth:sanctum'])
        ->get('/{room_id}', [RoomController::class, 'show'])
        ->name('rooms.show');
    Route::middleware(['auth:sanctum'])
        ->post('/{room_id}/join', [RoomController::class, 'join'])
        ->name('rooms.join');
    Route::middleware(['auth:sanctum'])
        ->post('/leave', [RoomController::class, 'leave'])
        ->name('rooms.leave');
    Route::middleware(['auth:sanctum'])
        ->post('/', [RoomController::class, 'store'])
        ->name('rooms.store');
});
