<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\GraphFetchOperation;
use SocalNick\Orchestrate\GraphPutOperation;
use SocalNick\Orchestrate\GraphDeleteOperation;
use SocalNick\Orchestrate\GraphObject;

class GraphTest extends \PHPUnit_Framework_TestCase
{
  protected static $client;

  public static function setUpBeforeClass()
  {
    self::$client = new Client(getenv('ORCHESTRATE_API_KEY'));
  }

  public function testPut()
  {
    $graphPutOp = new GraphPutOperation("films", "the_godfather", "sequel", "films", "the_godfather_part_2");
    $result = self::$client->execute($graphPutOp);
    $this->assertTrue($result);

    $graphPutOp = new GraphPutOperation("films", "the_godfather_part_2", "sequel", "films", "the_godfather_part_3");
    $result = self::$client->execute($graphPutOp);
    $this->assertTrue($result);

    $graphPutOp = new GraphPutOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather");
    $result = self::$client->execute($graphPutOp);
    $this->assertTrue($result);

    $graphPutOp = new GraphPutOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather_part_2");
    $result = self::$client->execute($graphPutOp);
    $this->assertTrue($result);

    $graphPutOp = new GraphPutOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather_part_3");
    $result = self::$client->execute($graphPutOp);
    $this->assertTrue($result);
  }

  public function testGet()
  {
    $graphFetchOp = new GraphFetchOperation("films", "the_godfather", "sequel/sequel");
    $graphObject = self::$client->execute($graphFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\GraphObject', $graphObject);
    $this->assertEquals(1, $graphObject->count());
    $value = $graphObject->getValue();
    $this->assertInternalType('array', $value);
    $this->assertArrayHasKey('results', $value);
    $results = $value['results'];
    $this->assertInternalType('array', $results);
    $this->assertArrayHasKey('path', $results[0]);
    $this->assertArrayHasKey('value', $results[0]);
  }

  public function testGetWithLimitOffset()
  {
    $graphFetchOp = new GraphFetchOperation("directors", "francis_ford_coppola", "films_directed", 3, 0);
    $graphObject = self::$client->execute($graphFetchOp);
    $this->assertEquals(3, $graphObject->count());

    $graphFetchOp = new GraphFetchOperation("directors", "francis_ford_coppola", "films_directed", 1, 1);
    $graphObject = self::$client->execute($graphFetchOp);
    $this->assertEquals(1, $graphObject->count());
    $this->assertEquals('/v0/directors/francis_ford_coppola/relations/films_directed?offset=2&limit=1', $graphObject->getNext());
    $this->assertEquals('/v0/directors/francis_ford_coppola/relations/films_directed?offset=0&limit=1', $graphObject->getPrev());
  }

  public function testDelete()
  {
    $graphDeleteOp = new GraphDeleteOperation("films", "the_godfather", "sequel", "films", "the_godfather_part_2");
    $result = self::$client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphDeleteOp = new GraphDeleteOperation("films", "the_godfather_part_2", "sequel", "films", "the_godfather_part_3");
    $result = self::$client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphDeleteOp = new GraphDeleteOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather");
    $result = self::$client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphDeleteOp = new GraphDeleteOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather_part_2");
    $result = self::$client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphDeleteOp = new GraphDeleteOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather_part_3");
    $result = self::$client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphFetchOp = new GraphFetchOperation("films", "the_godfather", "sequel");
    $graphObject = self::$client->execute($graphFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\GraphObject', $graphObject);
    $this->assertEquals(0, $graphObject->count());

    $graphFetchOp = new GraphFetchOperation("films", "the_godfather_part_2", "sequel");
    $graphObject = self::$client->execute($graphFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\GraphObject', $graphObject);
    $this->assertEquals(0, $graphObject->count());

    $graphFetchOp = new GraphFetchOperation("directors", "francis_ford_coppola", "films_directed");
    $graphObject = self::$client->execute($graphFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\GraphObject', $graphObject);
    $this->assertEquals(0, $graphObject->count());
  }

}
