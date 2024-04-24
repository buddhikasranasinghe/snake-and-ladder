<?php

namespace Tests\Feature;

use Tests\TestCase;

class DiceRollingTest extends TestCase
{
    /** @test */
    public function when_dice_rolling()
    {
        $response = $this->getJson('api/dice-rolling');

        $this->assertRandomNumberReceived($response);
    }

    protected function assertRandomNumberReceived($response): void
    {
        $response->assertOk();

        $this->assertLessThan(7, $randomValue = $response->json());
        $this->assertGreaterThan(1, $randomValue);
        $this->assertIsInt($randomValue);
    }
}
