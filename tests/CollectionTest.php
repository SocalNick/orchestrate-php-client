<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\KvDeleteOperation;
use SocalNick\Orchestrate\KvFetchOperation;
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
    $firstKeyResponse->shouldReceive('getHeader')
      ->with('ETag')
      ->andReturn('7b767e7cc8bdd6cb');
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
}
