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
    Route::get('/{room_id}', [RoomController::class, 'show'])->name('room.show');
    Route::post('/{room_id}/join', [RoomController::class, 'join'])->name('room.join');
    Route::post('/leave', [RoomController::class, 'leave'])->name('room.leave');
    Route::post('/', [RoomController::class, 'store'])->name('room.store');
});

Route::prefix('games')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [GameController::class, 'store'])->name('game.store');
    Route::post('/{game_id}/join', [GameController::class, 'join'])->name('game.join');
    // TODO temporary solution for testing
    Route::delete('/{game_id}/purge', [GameController::class, 'purge'])->name('game.purge');
    Route::get('/{game_id}/rounds', [GameController::class, 'getRounds'])->name('game.get_rounds');
    Route::post('/{game_id}/generate-number', [GameController::class, 'generateNumber'])
        ->name('game.generate_number');
});

// temporary endpoint for testing purposes
Route::delete('/user', [AuthController::class, 'deleteUser'])->name('user.delete');
