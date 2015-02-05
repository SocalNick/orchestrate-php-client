<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\EventListOperation;
use SocalNick\Orchestrate\EventPutOperation;
use SocalNick\Orchestrate\EventListObject;

class EventTest extends \PHPUnit_Framework_TestCase
{
  protected static $client;
  protected static $now;

  public static function setUpBeforeClass()
  {
    self::$client = new Client(getenv('ORCHESTRATE_API_KEY'));
    self::$now = (int) microtime(true) * 1000;

    $evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(["message" => "This is a test event"]), self::$now);
    $result = self::$client->execute($evPutOp);
  }

  public function testEventPutDefaultsToNow()
  {
    $evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(["message" => "This is my favorite movie!"]));
    $result = self::$client->execute($evPutOp);
    $this->assertTrue($result);
  }

  public function testEventPutWithTimestamp()
  {
    $evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(["message" => "This is my favorite movie!"]), self::$now);
    $result = self::$client->execute($evPutOp);
    $this->assertTrue($result);
  }

  public function testList()
  {
    $evListOp = new EventListOperation("films", "pulp_fiction", "comment");
    $evListObject = self::$client->execute($evListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventListObject', $evListObject);
    $this->assertGreaterThan(2, $evListObject->count());
    $value = $evListObject->getValue();
    $this->assertInternalType('array', $value);
    $this->assertArrayHasKey('results', $value);
    $results = $value['results'];
    $this->assertInternalType('array', $results);
  }

  public function testListWithLimit()
  {
    $evListOp = new EventListOperation("films", "pulp_fiction", "comment", 2);
    $evListObject = self::$client->execute($evListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventListObject', $evListObject);
    $this->assertEquals(2, $evListObject->count());
    $value = $evListObject->getValue();
    $this->assertInternalType('array', $value);
    $this->assertArrayHasKey('results', $value);
    $results = $value['results'];
    $this->assertInternalType('array', $results);
  }

  public function testListWithStartEventEndEvent()
  {
    $evListOp = new EventListOperation("films", "pulp_fiction", "comment", 1, self::$now, null, null, self::$now+1);
    $evListObject = self::$client->execute($evListOp);
    $this->assertEquals(1, $evListObject->count());
    $value = $evListObject->getValue();
    $results = $value['results'];
    $this->assertEquals(self::$now, $results[0]['timestamp']);
  }

  public function testListWithAfterEvent()
  {
    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => __FUNCTION__]));
    $evPostResult = self::$client->execute($evPostOp);

    $evListOp = new EventListOperation("films", "pulp_fiction", "comment", 1, null, $evPostResult->getTimestamp() . '/' . $evPostResult->getOrdinal());
    $evListObject = self::$client->execute($evListOp);
    $this->assertEquals(0, $evListObject->count());
  }

  public function testListWithBeforeEvent()
  {
    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => __FUNCTION__]));
    $evPostResult = self::$client->execute($evPostOp);

    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => "Testing event to be ignored"]));
    $evPostResult = self::$client->execute($evPostOp);

    $evListOp = new EventListOperation("films", "pulp_fiction", "comment", 1, null, null, $evPostResult->getTimestamp() . '/' . $evPostResult->getOrdinal());
    $evListObject = self::$client->execute($evListOp);
    $this->assertEquals(1, $evListObject->count());
    $value = $evListObject->getValue();
    $results = $value['results'];
    $this->assertEquals(__FUNCTION__, $results[0]['value']['message']);
  }
}
