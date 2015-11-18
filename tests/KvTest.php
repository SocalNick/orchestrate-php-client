<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\CollectionDeleteOperation;
use SocalNick\Orchestrate\KvDeleteOperation;
use SocalNick\Orchestrate\KvFetchOperation;
use SocalNick\Orchestrate\KvListOperation;
use SocalNick\Orchestrate\KvPatchMergeOperation;
use SocalNick\Orchestrate\KvPatchOperationsOperation;
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

    $kvPutOp = new KvPutOperation(self::$collection, uniqid(), json_encode(["name" => "Nick"]));
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
    $kvPutOp = new KvPutOperation(self::$collection, $key, json_encode(["name" => "John"]));
    $kvObject = self::$client->execute($kvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('741357981fd7b5cb', $kvObject->getRef());
  }

  public function testPutIfMatch()
  {
    $key = uniqid();
    $originalKvPutOp = new KvPutOperation(self::$collection, $key, json_encode(["name" => "Terry"]));
    $originalKvObject = self::$client->execute($originalKvPutOp);
    $originalRef = $originalKvObject->getRef();

    $kvPutOp = new KvPutOperation(self::$collection, $key, json_encode(["name" => "Terrance"]), ['if-match' => $originalRef]);
    $kvObject = self::$client->execute($kvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertNotEquals($originalRef, $kvObject->getRef());
  }

  public function testPutIfNoneMatch()
  {
    $key = uniqid();
    $originalKvPutOp = new KvPutOperation(self::$collection, $key, json_encode(["name" => "William"]));
    $originalKvObject = self::$client->execute($originalKvPutOp);

    $this->setExpectedException('SocalNick\Orchestrate\Exception\ClientException');
    $kvPutOp = new KvPutOperation(self::$collection, $key, json_encode(["name" => "Bill"]), ['if-none-match' => '*']);
    $kvObject = self::$client->execute($kvPutOp);
  }

  public function testPost()
  {
    $kvPostOp = new KvPostOperation(self::$collection, json_encode(["name" => "Adam"]));
    $kvObject = self::$client->execute($kvPostOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertRegExp('/[a-f0-9]{16}/', $kvObject->getKey());
  }

  public function testKeyDoesNotExist404()
  {
    $this->setExpectedException('SocalNick\Orchestrate\Exception\ClientException');
    $kvFetchOp = new KvFetchOperation(self::$collection, "missing_key");
    $kvObject = self::$client->execute($kvFetchOp);
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
    $originalKvPutOp = new KvPutOperation(self::$collection, $key, json_encode(["name" => "Leah"]));
    $originalKvObject = self::$client->execute($originalKvPutOp);
    $originalRef = $originalKvObject->getRef();
    $updateKvPutOp = new KvPutOperation(self::$collection, $key, json_encode(["name" => "Allegra"]));
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
    $this->assertEquals('/v0/films?afterKey=the_godfather_part_2&limit=10', $kvListObject->getLink());
  }

  public function testListInclusiveStartKey()
  {
    $kvListOp = new KvListOperation("films", 5, 'anchorman');
    $kvListObject = self::$client->execute($kvListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvListObject', $kvListObject);
    $this->assertEquals(5, $kvListObject->count());
    $this->assertEquals('/v0/films?afterKey=pulp_fiction&limit=5', $kvListObject->getLink());
  }

  public function testListExclusiveAfterKey()
  {
    $kvListOp = new KvListOperation("films", 5, null, 'anchorman');
    $kvListObject = self::$client->execute($kvListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvListObject', $kvListObject);
    $this->assertEquals(5, $kvListObject->count());
    $this->assertEquals('/v0/films?afterKey=shawshank_redemption&limit=5', $kvListObject->getLink());
  }

  public function testPatchOperations()
  {
    $key = uniqid();
    $originalKvPutOp = new KvPutOperation(self::$collection, $key, json_encode([
      "first_name" => "John",
      "full_name" => "John Foster",
      "age" => 28,
      "years_until_death" => 40,
      "birth_place" => [
        "state" => "California",
        "country" => "USA",
      ],
    ]));
    $originalKvObject = self::$client->execute($originalKvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $originalKvObject);

    $kvPatchOperationsOp = new KvPatchOperationsOperation(self::$collection, $key);
    $kvPatchOperationsOp
      ->add('birth_place.city', 'New York')
      ->remove('birth_place.country')
      ->replace('birth_place.state', 'New York')
      ->move('first_name', 'deprecated_first_name')
      ->copy('full_name', 'name')
      ->test('age', 28)
      ->inc('age', 1)
      ->inc('years_until_death', -1);
    $result = self::$client->execute($kvPatchOperationsOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $result);

    $kvFetchOp = new KvFetchOperation(self::$collection, $key);
    $kvObject = self::$client->execute($kvFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $value = $kvObject->getValue();
    $this->assertEquals('New York', $value['birth_place']['city']);
    $this->assertArrayNotHasKey('country', $value['birth_place']);
    $this->assertEquals('New York', $value['birth_place']['state']);
    $this->assertEquals('John Foster', $value['name']);
    $this->assertEquals(29, $value['age']);
    $this->assertEquals(39, $value['years_until_death']);
  }

  public function testPatchMerge()
  {
    $key = uniqid();
    $originalKvPutOp = new KvPutOperation(self::$collection, $key, json_encode([
      "first_name" => "John",
      "full_name" => "John Foster",
      "age" => 28,
      "years_until_death" => 40,
      "birth_place" => [
        "state" => "California",
        "country" => "USA",
      ],
    ]));
    $originalKvObject = self::$client->execute($originalKvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $originalKvObject);

    $partial = [
      'birth_place' => [
        'city' => 'New York',
        'country' => null,
        'state' => 'New York',
      ],
      'first_name' => null,
      'deprecated_first_name' => 'John',
      'name' => 'John Foster',
      'age' => 29,
      'years_until_death' => 39,
    ];

    $kvPatchMergeOp = new KvPatchMergeOperation(self::$collection, $key, json_encode($partial));
    $result = self::$client->execute($kvPatchMergeOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $result);

    $kvFetchOp = new KvFetchOperation(self::$collection, $key);
    $kvObject = self::$client->execute($kvFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $value = $kvObject->getValue();
    $this->assertEquals('New York', $value['birth_place']['city']);
    $this->assertArrayNotHasKey('country', $value['birth_place']);
    $this->assertEquals('New York', $value['birth_place']['state']);
    $this->assertEquals('John Foster', $value['name']);
    $this->assertEquals(29, $value['age']);
    $this->assertEquals(39, $value['years_until_death']);
  }
}
