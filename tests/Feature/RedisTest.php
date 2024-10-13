<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RedisTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testPing(): void
    {
        $response = Redis::command("ping");
        self::assertEquals("PONG", $response);

        $repsonse = Redis::ping();
        self::assertEquals("PONG", $response);
    }

    public function testSet()
    {

        Redis::setEx("username", 2, "Jabriel");
        $response = Redis::get("username");

        sleep(5);

        $response = Redis::get("username");
        self::assertNull($response);

    }


}
