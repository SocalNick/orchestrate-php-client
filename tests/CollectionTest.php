<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\CollectionDeleteOperation;
use SocalNick\Orchestrate\KvPutOperation;
use SocalNick\Orchestrate\KvObject;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
  protected static $collection;
  protected static $client;

  public static function setUpBeforeClass()
  {
    self::$collection = uniqid('collection-');
    self::$client = new Client(getenv('ORCHESTRATE_API_KEY'));
  }

  public function testPutWithoutCollectionCreatesCollection()
  {
    $kvPutOp = new KvPutOperation(self::$collection, uniqid(), json_encode(["name" => "Nick"]));
    $kvObject = self::$client->execute($kvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('7b767e7cc8bdd6cb', $kvObject->getRef());
  }

  public function testDeleteCollection()
  {
    $cDeleteOp = new CollectionDeleteOperation(self::$collection);
    $result = self::$client->execute($cDeleteOp);
    $this->assertTrue($result);
  }
}
