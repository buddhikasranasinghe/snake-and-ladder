<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckPlayerExists;
use App\Http\Controllers\MakePlayersController;
use App\Http\Controllers\DiceRollingController;
use App\Http\Controllers\InitialRollingController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('make-players', MakePlayersController::class);

Route::middleware([CheckPlayerExists::class])->group(function () {
    Route::get('initial-rolling/{player_key}', InitialRollingController::class);
    Route::get('dice-rolling/{player_key}', DiceRollingController::class);
});
