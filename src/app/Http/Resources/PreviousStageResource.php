<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PreviousStageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'players' => $this->players
        ];
    }
}
