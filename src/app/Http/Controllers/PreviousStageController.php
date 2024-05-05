<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Domain\Actions\FindPreviousStageAction;
use App\Http\Resources\PreviousStageResource;

class PreviousStageController extends Controller
{
    public function index(FindPreviousStageAction $action): JsonResponse
    {
        $prevStage = $action->execute();

        return response()->json(new PreviousStageResource($prevStage));
    }
}
