<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
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
    Route::middleware(['validated.tg'])
        ->post('/', [AuthController::class, 'authenticate'])
        ->name('auth');

    Route::middleware(['validated.tg'])
        ->post('/regenerate-token', [AuthController::class, 'regenerateToken'])
        ->name('auth.regenerate_token');

    Route::middleware(['auth:sanctum'])
        ->get('/user', [AuthController::class, 'user'])
        ->name('auth.user');
});

Route::prefix('rooms')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/{room_id}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('/{room_id}/join', [RoomController::class, 'join'])->name('rooms.join');
    Route::post('/leave', [RoomController::class, 'leave'])->name('rooms.leave');
    Route::post('/', [RoomController::class, 'store'])->name('rooms.store');
});

Route::prefix('games')->group(function () {
    Route::post('/', [GameController::class, 'store'])->name('games.store');
    Route::post('/{game_id}/generate-number', [GameController::class, 'generateNumber'])
        ->name('games.generate_number');
});
