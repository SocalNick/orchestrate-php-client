<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\EventFetchOperation;
use SocalNick\Orchestrate\EventPutOperation;
use SocalNick\Orchestrate\EventObject;

class EventTest extends \PHPUnit_Framework_TestCase
{
  protected static $client;
  protected static $now;

  public static function setUpBeforeClass()
  {
    self::$client = new Client(getenv('ORCHESTRATE_API_KEY'));
    self::$now = (int) microtime(true) * 1000;
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

  public function testGet()
  {
    $evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment");
    $evObject = self::$client->execute($evFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventObject', $evObject);
    $this->assertGreaterThan(2, $evObject->count());
    $value = $evObject->getValue();
    $this->assertInternalType('array', $value);
    $this->assertArrayHasKey('results', $value);
    $results = $value['results'];
    $this->assertInternalType('array', $results);
  }

  public function testGetWithStartEnd()
  {
    $evGetOp = new EventFetchOperation("films", "pulp_fiction", "comment", self::$now, self::$now+1);
    $evObject = self::$client->execute($evGetOp);
    $this->assertEquals(1, $evObject->count());
    $value = $evObject->getValue();
    $results = $value['results'];
    $this->assertEquals(self::$now, $results[0]['timestamp']);
  }

}
