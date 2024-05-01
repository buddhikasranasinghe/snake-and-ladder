<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MakePlayersController;
use App\Http\Controllers\DiceRollingController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('make-players', MakePlayersController::class);
Route::get('dice-rolling', DiceRollingController::class);
