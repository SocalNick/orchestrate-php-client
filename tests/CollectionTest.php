<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\CollectionDeleteOperation;
use SocalNick\Orchestrate\KvPutOperation;
use SocalNick\Orchestrate\KvObject;
use Mockery as m;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
  protected $client;

  protected function setUp()
  {
    $httpClient = m::mock('Guzzle\Http\ClientInterface');

    $firstKeyResponse = m::mock('Guzzle\Http\Message\Response');
    $firstKeyResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(true);
    $firstKeyResponse->shouldReceive('getHeader')
      ->with('ETag')
      ->andReturn('7b767e7cc8bdd6cb');
    $firstKeyResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(true);
    $firstKeyResponse->shouldReceive('getLocation')
      ->andReturn('/v0/first_collection/first_key/refs/7b767e7cc8bdd6cb');
    $firstKeyResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $firstKeyResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $firstKeyRequest = m::mock('Guzzle\Http\Message\Request');
    $firstKeyRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $firstKeyRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($firstKeyResponse);
    $httpClient->shouldReceive('put')
      ->with('first_collection/first_key', array('Content-Type'=>'application/json'), '{"name":"Nick"}')
      ->andReturn($firstKeyRequest);

    $deleteResponse = m::mock('Guzzle\Http\Message\Response');
    $deleteResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $deleteResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $deleteResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(false);
    $deleteResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $deleteResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $deleteRequest = m::mock('Guzzle\Http\Message\Request');
    $deleteRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $deleteRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($deleteResponse);
    $httpClient->shouldReceive('delete')
      ->with('first_collection?force=true')
      ->andReturn($deleteRequest);

    $this->client = new Client('api-key', $httpClient);
  }

  public function tearDown()
  {
    m::close();
  }

  public function testPutWithoutCollectionCreatesCollection()
  {
    $kvPutOp = new KvPutOperation("first_collection", "first_key", json_encode(array("name" => "Nick")));
    $kvObject = $this->client->execute($kvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('7b767e7cc8bdd6cb', $kvObject->getRef());
  }

  public function testDeleteCollection()
  {
    $cDeleteOp = new CollectionDeleteOperation("first_collection");
    $result = $this->client->execute($cDeleteOp);
    $this->assertTrue($result);
  }
}
