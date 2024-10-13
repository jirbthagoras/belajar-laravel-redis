<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Predis\Command\Argument\Geospatial\ByRadius;
use Predis\Command\Argument\Geospatial\FromLonLat;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;

class RedisTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testPing(): void
    {
        $response = Redis::command("ping");
        self::assertEquals("PONG", $response);

        $response = Redis::ping();
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

    public function testList()
    {

        Redis::del("friends");
        Redis::rpush("friends", "Jabriel");
        Redis::rpush("friends", "Iqbal");
        Redis::rpush("friends", "Varen");

        $response = Redis::lrange("friends", 0, -1);

        assertEquals(["Jabriel", "Iqbal", "Varen"], $response);

        self::assertEquals("Jabriel", Redis::lpop("friends"));
        self::assertEquals("Iqbal", Redis::lpop("friends"));
        self::assertEquals("Varen ", Redis::lpop("friends"));

    }

    public function testSets()
    {

        Redis::del("names");
        Redis::sadd("names", "Jabriel");
        Redis::sadd("names", "Jabriel");
        Redis::sadd("names", "Berak");
        Redis::sadd("names", "Berak");
        Redis::sadd("names", "Dicelana");
        Redis::sadd("names", "Dicelana");

        $response = Redis::smembers("names");
        self::assertEquals(["Jabriel", "Berak", "Dicelana"], $response);

    }

    public function testSortedSets()
    {

        Redis::del("names");

        Redis::zadd("names", 100, "Jabriel");
        Redis::zadd("names", 85, "Varen");
        Redis::zadd("names", 95, "Iqbal");

        $response = Redis::zrange("names", 0, -1);

        self::assertEquals(["Varen", "Iqbal", "Jabriel"], $response);

        $response = Redis::zrange("names", 95, 100, "byscore");

        self::assertEquals(["Iqbal", "Jabriel"], $response);

    }

    public function testHash()
    {

        Redis::del("user:1");
        Redis::hset("user:1", "name", "Jabriel");
        Redis::hset("user:1", "kelas", "XI PPLG 2");
        Redis::hset("user:1", "age", 17);

        $response = Redis::hgetall("user:1");
        self::assertEquals([
            "name" => "Jabriel",
            "kelas" => "XI PPLG 2",
            "age" => 17
        ], $response);

    }

    public function testGeoPoint()
    {

        Redis::del("sellers");
        Redis::geoadd("sellers", 106.822702, -6.177590, "Toko A");
        Redis::geoadd("sellers", 106.820889, -6.174964, "Toko B");

        $result = Redis::geodist("sellers", "Toko A", "Toko B", "km");
        self::assertEquals(0.3543, $result);
    }

    public function testHyperLogLog()
    {
        Redis::pfadd("visitors", "eko", "kurniawan", "khannedy");
        Redis::pfadd("visitors", "eko", "budi", "khannedy");
        Redis::pfadd("visitors", "rully", "budi", "khannedy");

        $total = Redis::pfcount("visitors");

        self::assertEquals(5, $total);

    }


}
