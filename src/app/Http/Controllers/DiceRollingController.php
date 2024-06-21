<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Src\Utilities\FormRequest;
use Illuminate\Http\JsonResponse;
use Src\Domain\Actions\DiceRollingAction;
use Domain\Exceptions\InvalidPlayerException;

class DiceRollingController extends Controller
{
    public function __invoke(FormRequest $request, DiceRollingAction $action): JsonResponse
    {
        try {
            $score = $action->execute($request->getPlayer());

            return response()->json($score, Response::HTTP_OK);
        } catch (InvalidPlayerException $e) {
            return response()->json($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
