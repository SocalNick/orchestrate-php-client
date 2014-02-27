<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
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

    $secondKeyResponse = m::mock('Guzzle\Http\Message\Response');
    $secondKeyResponse->shouldReceive('getHeader')
      ->with('ETag')
      ->andReturn('741357981fd7b5cb');
    $secondKeyResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $secondKeyResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $secondKeyRequest = m::mock('Guzzle\Http\Message\Request');
    $secondKeyRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $secondKeyRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($secondKeyResponse);
    $httpClient->shouldReceive('put')
      ->with('first_collection/second_key', array('Content-Type'=>'application/json'), '{"name":"John"}')
      ->andReturn($secondKeyRequest);

    $missingRequest = m::mock('Guzzle\Http\Message\Request');
    $missingRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $missingRequest->shouldReceive('send')
      ->withNoArgs()
      ->andThrow(new \Guzzle\Http\Exception\ClientErrorResponseException());
    $httpClient->shouldReceive('get')
      ->with('first_collection/missing_key')
      ->andReturn($missingRequest);

    $godfatherResponse = m::mock('Guzzle\Http\Message\Response');
    $godfatherResponse->shouldReceive('getHeader')
      ->with('ETag')
      ->andReturn('9c1bc18e60d93848');
    $godfatherResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"Title": "The Godfather","Released": "24 Mar 1972"}', true));
    $godfatherResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"Title": "The Godfather","Released": "24 Mar 1972"}');
    $godfatherRequest = m::mock('Guzzle\Http\Message\Request');
    $godfatherRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $godfatherRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($godfatherResponse);
    $httpClient->shouldReceive('get')
      ->with('films/the_godfather')
      ->andReturn($godfatherRequest);

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

  public function testPutWithCollection()
  {
    $kvPutOp = new KvPutOperation("first_collection", "second_key", json_encode(array("name" => "John")));
    $kvObject = $this->client->execute($kvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('741357981fd7b5cb', $kvObject->getRef());
  }

  public function testKeyDoesNotExist404()
  {
    $kvFetchOp = new KvFetchOperation("first_collection", "missing_key");
    $kvObject = $this->client->execute($kvFetchOp);
    $this->assertNull($kvObject);
  }

  public function testGet()
  {
    $kvFetchOp = new KvFetchOperation("films", "the_godfather");
    $kvObject = $this->client->execute($kvFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('9c1bc18e60d93848', $kvObject->getRef());
    $value = $kvObject->getValue();
    $this->assertArrayHasKey('Title', $value);
    $this->assertArrayHasKey('Released', $value);
    $this->assertEquals('The Godfather', $value['Title']);
    $this->assertEquals('24 Mar 1972', $value['Released']);
  }
}
