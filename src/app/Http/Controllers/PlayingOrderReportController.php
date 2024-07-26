<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Domain\Actions\ViewPlayingOrderAction;

class PlayingOrderReportController extends Controller
{
    public function __invoke(ViewPlayingOrderAction $action): JsonResponse
    {
        $report = $action->execute();

        return response()->json(['report' => $report]);
    }
}
