<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\EventDeleteOperation;
use SocalNick\Orchestrate\EventFetchOperation;
use SocalNick\Orchestrate\EventListOperation;
use SocalNick\Orchestrate\EventPostOperation;
use SocalNick\Orchestrate\EventPutOperation;
use SocalNick\Orchestrate\EventListObject;

class EventTest extends \PHPUnit_Framework_TestCase
{
  protected static $client;
  protected static $now;
  protected static $ordinalOne;
  protected static $ordinalTwo;

  public static function setUpBeforeClass()
  {
    self::$client = new Client(getenv('ORCHESTRATE_API_KEY'));
    self::$now = (int) microtime(true) * 1000;

    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => "This is a test event"]), self::$now);
    $evPostResult = self::$client->execute($evPostOp);
    self::$ordinalOne = $evPostResult->getOrdinal();

    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => "This is another test event"]), self::$now);
    $evPostResult = self::$client->execute($evPostOp);
    self::$ordinalTwo = $evPostResult->getOrdinal();
  }

  public function testEventFetch()
  {
    $evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment", self::$now, self::$ordinalOne);
    $evObject = self::$client->execute($evFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventObject', $evObject);
    $value = $evObject->getValue();
    $this->assertEquals('This is a test event', $value['value']['message']);

    $evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment", self::$now, self::$ordinalTwo);
    $evObject = self::$client->execute($evFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventObject', $evObject);
    $value = $evObject->getValue();
    $this->assertEquals('This is another test event', $value['value']['message']);
  }

  public function testEventPostDefaultsToNow()
  {
    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => "This is my favorite movie!"]));
    $evPostResult = self::$client->execute($evPostOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventUpsertResult', $evPostResult);
    $this->assertGreaterThan(self::$now - 10000, $evPostResult->getTimestamp());
    $this->assertLessThan(self::$now + 10000, $evPostResult->getTimestamp());
    $this->assertTrue(is_numeric($evPostResult->getOrdinal()));
  }

  public function testEventPostWithTimestamp()
  {
    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => "This is my favorite movie!"]), self::$now);
    $evPostResult = self::$client->execute($evPostOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventUpsertResult', $evPostResult);
    $this->assertEquals(self::$now, $evPostResult->getTimestamp());
    $this->assertTrue(is_numeric($evPostResult->getOrdinal()));
  }

  public function testEventPut()
  {
    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => __FUNCTION__]));
    $evPostResult = self::$client->execute($evPostOp);

    $evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(["message" => "Not this function"]), $evPostResult->getTimestamp(), $evPostResult->getOrdinal());
    $evPutResult = self::$client->execute($evPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventUpsertResult', $evPutResult);
    $this->assertEquals($evPostResult->getTimestamp(), $evPutResult->getTimestamp());
    $this->assertEquals($evPostResult->getOrdinal(), $evPutResult->getOrdinal());

    $evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment", $evPostResult->getTimestamp(), $evPostResult->getOrdinal());
    $evObject = self::$client->execute($evFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventObject', $evObject);
    $value = $evObject->getValue();
    $this->assertEquals('Not this function', $value['value']['message']);
  }

  public function testEventPutWithBadRef()
  {
    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => __FUNCTION__]));
    $evPostResult = self::$client->execute($evPostOp);

    $this->setExpectedException('SocalNick\Orchestrate\Exception\ClientException');
    $evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(["message" => "Not this function"]), $evPostResult->getTimestamp(), $evPostResult->getOrdinal(), 'bad-ref');
    $evPutResult = self::$client->execute($evPutOp);
  }

  public function testEventPutWithRef()
  {
    $evPostOp = new EventPostOperation("films", "pulp_fiction", "comment", json_encode(["message" => __FUNCTION__]));
    $evPostResult = self::$client->execute($evPostOp);

    $evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(["message" => "Not this function"]), $evPostResult->getTimestamp(), $evPostResult->getOrdinal(), $evPostResult->getRef());
    $evPutResult = self::$client->execute($evPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventUpsertResult', $evPutResult);
    $this->assertEquals($evPostResult->getTimestamp(), $evPutResult->getTimestamp());
    $this->assertEquals($evPostResult->getOrdinal(), $evPutResult->getOrdinal());

    $evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment", $evPostResult->getTimestamp(), $evPostResult->getOrdinal());
    $evObject = self::$client->execute($evFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventObject', $evObject);
    $value = $evObject->getValue();
    $this->assertEquals('Not this function', $value['value']['message']);
  }

  public function testEventDelete()
  {
    $evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment", self::$now, self::$ordinalOne);
    $evObject = self::$client->execute($evFetchOp);

    $evDeleteOp = new EventDeleteOperation("films", "pulp_fiction", "comment", self::$now, self::$ordinalOne, true);
    $result = self::$client->execute($evDeleteOp);
    $this->assertTrue($result);
  }

  public function testEventDeleteWithBadRef()
  {
    $evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment", self::$now, self::$ordinalTwo);
    $evObject = self::$client->execute($evFetchOp);

    $this->setExpectedException('SocalNick\Orchestrate\Exception\ClientException');
    $evDeleteOp = new EventDeleteOperation("films", "pulp_fiction", "comment", self::$now, self::$ordinalTwo, true, 'bad-ref');
    $result = self::$client->execute($evDeleteOp);
  }

  public function testEventDeleteWithRef()
  {
    $evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment", self::$now, self::$ordinalTwo);
    $evObject = self::$client->execute($evFetchOp);

    $evDeleteOp = new EventDeleteOperation("films", "pulp_fiction", "comment", self::$now, self::$ordinalTwo, true, $evObject->getRef());
    $result = self::$client->execute($evDeleteOp);
    $this->assertTrue($result);
  }

  public function testList()
  {
    $evListOp = new EventListOperation("films", "pulp_fiction", "comment");
    $evListObject = self::$client->execute($evListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventListObject', $evListObject);
    $this->assertGreaterThan(2, $evListObject->count());
    $this->assertRegExp('#/v0/films/pulp_fiction/events/comment\?limit=10&beforeEvent=[0-9]+/[0-9]+#', $evListObject->getNext());
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
