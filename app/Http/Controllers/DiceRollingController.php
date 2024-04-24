<?php

namespace App\Http\Controllers;

use Src\DiceRollingAction;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class DiceRollingController extends Controller
{
    public function __invoke(DiceRollingAction $action): JsonResponse
    {
        return response()->json($action->execute(), Response::HTTP_OK);
    }
}
