<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LabyrinthPageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        // В тз не указано, по какому адресу мы генерим лабиринт, поэтому положим в /generate
        $response = $this->get('/generate');

        $response->assertStatus(200);
    }
}
