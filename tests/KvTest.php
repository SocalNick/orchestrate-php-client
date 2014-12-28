<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\CollectionDeleteOperation;
use SocalNick\Orchestrate\KvDeleteOperation;
use SocalNick\Orchestrate\KvFetchOperation;
use SocalNick\Orchestrate\KvListOperation;
use SocalNick\Orchestrate\KvPostOperation;
use SocalNick\Orchestrate\KvPutOperation;
use SocalNick\Orchestrate\KvObject;
use SocalNick\Orchestrate\KvListObject;

class KvTest extends \PHPUnit_Framework_TestCase
{
  protected static $collection;
  protected static $client;

  public static function setUpBeforeClass()
  {
    self::$collection = uniqid('collection-');
    self::$client = new Client(getenv('ORCHESTRATE_API_KEY'));

    $kvPutOp = new KvPutOperation(self::$collection, uniqid(), json_encode(array("name" => "Nick")));
    $kvObject = self::$client->execute($kvPutOp);
  }

  public static function tearDownAfterClass()
  {
    $cDeleteOp = new CollectionDeleteOperation(self::$collection);
    $result = self::$client->execute($cDeleteOp);
  }

  public function testPutWithCollection()
  {
    $key = uniqid();
    $kvPutOp = new KvPutOperation(self::$collection, $key, json_encode(array("name" => "John")));
    $kvObject = self::$client->execute($kvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('741357981fd7b5cb', $kvObject->getRef());
  }

  public function testPutIfMatch()
  {
    $key = uniqid();
    $originalKvPutOp = new KvPutOperation(self::$collection, $key, json_encode(array("name" => "Terry")));
    $originalKvObject = self::$client->execute($originalKvPutOp);
    $originalRef = $originalKvObject->getRef();

    $kvPutOp = new KvPutOperation(self::$collection, $key, json_encode(array("name" => "Terrance")), array('if-match' => $originalRef));
    $kvObject = self::$client->execute($kvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertNotEquals($originalRef, $kvObject->getRef());
  }

  public function testPutIfNoneMatch()
  {
    $key = uniqid();
    $originalKvPutOp = new KvPutOperation(self::$collection, $key, json_encode(array("name" => "William")));
    $originalKvObject = self::$client->execute($originalKvPutOp);

    $kvPutOp = new KvPutOperation(self::$collection, $key, json_encode(array("name" => "Bill")), array('if-none-match' => '*'));
    $kvObject = self::$client->execute($kvPutOp);
    $this->assertNull($kvObject);
  }

  public function testPost()
  {
    $kvPostOp = new KvPostOperation(self::$collection, json_encode(array("name" => "Adam")));
    $kvObject = self::$client->execute($kvPostOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertRegExp('/[a-f0-9]{16}/', $kvObject->getKey());
  }

  public function testKeyDoesNotExist404()
  {
    $kvFetchOp = new KvFetchOperation(self::$collection, "missing_key");
    $kvObject = self::$client->execute($kvFetchOp);
    $this->assertNull($kvObject);
  }

  public function testGet()
  {
    $kvFetchOp = new KvFetchOperation("films", "the_godfather");
    $kvObject = self::$client->execute($kvFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('9c1bc18e60d93848', $kvObject->getRef());
    $value = $kvObject->getValue();
    $this->assertArrayHasKey('Title', $value);
    $this->assertArrayHasKey('Released', $value);
    $this->assertEquals('The Godfather', $value['Title']);
    $this->assertEquals('24 Mar 1972', $value['Released']);
  }

  public function testGetPreviousRef()
  {
    $key = uniqid();
    $originalKvPutOp = new KvPutOperation(self::$collection, $key, json_encode(array("name" => "Leah")));
    $originalKvObject = self::$client->execute($originalKvPutOp);
    $originalRef = $originalKvObject->getRef();
    $updateKvPutOp = new KvPutOperation(self::$collection, $key, json_encode(array("name" => "Allegra")));
    $updateKvObject = self::$client->execute($updateKvPutOp);

    $kvFetchOp = new KvFetchOperation(self::$collection, $key, $originalRef);
    $kvObject = self::$client->execute($kvFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals($originalRef, $kvObject->getRef());
    $value = $kvObject->getValue();
    $this->assertArrayHasKey('name', $value);
    $this->assertEquals('Leah', $value['name']);
  }

  public function testDelete()
  {
    $kvDeleteOp = new KvDeleteOperation(self::$collection, "first_key");
    $result = self::$client->execute($kvDeleteOp);
    $this->assertTrue($result);
  }

  public function testDeleteWithPurge()
  {
    $kvDeleteOp = new KvDeleteOperation(self::$collection, "first_key", true);
    $result = self::$client->execute($kvDeleteOp);
    $this->assertTrue($result);
  }

  public function testListDefaultLimit()
  {
    $kvListOp = new KvListOperation("films", 10);
    $kvListObject = self::$client->execute($kvListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvListObject', $kvListObject);
    $this->assertEquals(10, $kvListObject->count());
    $this->assertEquals('/v0/films?limit=10&afterKey=the_godfather_part_2', $kvListObject->getLink());
  }

  public function testListInclusiveStartKey()
  {
    $kvListOp = new KvListOperation("films", 5, 'anchorman');
    $kvListObject = self::$client->execute($kvListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvListObject', $kvListObject);
    $this->assertEquals(5, $kvListObject->count());
    $this->assertEquals('/v0/films?limit=5&afterKey=pulp_fiction', $kvListObject->getLink());
  }

  public function testListExclusiveAfterKey()
  {
    $kvListOp = new KvListOperation("films", 5, null, 'anchorman');
    $kvListObject = self::$client->execute($kvListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvListObject', $kvListObject);
    $this->assertEquals(5, $kvListObject->count());
    $this->assertEquals('/v0/films?limit=5&afterKey=shawshank_redemption', $kvListObject->getLink());
  }
}
