<?php

namespace App\Http\Controllers;

use Src\Utilities\FormRequest;
use Illuminate\Http\JsonResponse;
use Domain\Exceptions\InvalidPlayerException;
use Domain\Actions\InitiallyDiceRollingAction;

class InitialRollingController extends Controller
{
    /**
     * @throws InvalidPlayerException
     */
    public function __invoke(InitiallyDiceRollingAction $action, FormRequest $request): JsonResponse
    {
        $action->execute($request->getPlayer());

        return response()->json();
    }
}
