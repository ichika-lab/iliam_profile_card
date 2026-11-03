<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DiceTest extends TestCase
{
    public function test_guest_can_open_dice_without_database_queries(): void
    {
        $this->withoutVite();
        $this->expectsDatabaseQueryCount(0);

        $this->get(route('dice'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Dice'));
    }
}
