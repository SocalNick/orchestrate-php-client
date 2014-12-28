<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\KvDeleteOperation;
use SocalNick\Orchestrate\KvFetchOperation;
use SocalNick\Orchestrate\KvListOperation;
use SocalNick\Orchestrate\KvPostOperation;
use SocalNick\Orchestrate\KvPutOperation;
use SocalNick\Orchestrate\KvObject;
use SocalNick\Orchestrate\KvListObject;
use Mockery as m;

class KvTest extends \PHPUnit_Framework_TestCase
{
  protected $client;

  protected function setUp()
  {
    $httpClient = m::mock('Guzzle\Http\ClientInterface');

    $secondKeyResponse = m::mock('Guzzle\Http\Message\Response');
    $secondKeyResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(true);
    $secondKeyResponse->shouldReceive('getHeader')
      ->with('ETag')
      ->andReturn('741357981fd7b5cb');
    $secondKeyResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(true);
    $secondKeyResponse->shouldReceive('getLocation')
      ->andReturn('/v0/first_collection/second_key/refs/741357981fd7b5cb');
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

    $secondKeyMatchResponse = m::mock('Guzzle\Http\Message\Response');
    $secondKeyMatchResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(true);
    $secondKeyMatchResponse->shouldReceive('getHeader')
      ->with('ETag')
      ->andReturn('0d1f15ab524a5c5a');
    $secondKeyMatchResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(true);
    $secondKeyMatchResponse->shouldReceive('getLocation')
      ->andReturn('/v0/first_collection/second_key/refs/0d1f15ab524a5c5a');
    $secondKeyMatchResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $secondKeyMatchResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $secondKeyMatchRequest = m::mock('Guzzle\Http\Message\Request');
    $secondKeyMatchRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $secondKeyMatchRequest->shouldReceive('setHeader')
      ->with(array('If-Match' => '741357981fd7b5cb'))
      ->andReturn(m::self());
    $secondKeyMatchRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($secondKeyMatchResponse);
    $httpClient->shouldReceive('put')
      ->with('first_collection/second_key', array('Content-Type'=>'application/json', 'If-Match'=>'"741357981fd7b5cb"'), '{"name":"Terry"}')
      ->andReturn($secondKeyMatchRequest);

    $secondKeyNoneMatchRequest = m::mock('Guzzle\Http\Message\Request');
    $secondKeyNoneMatchRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $secondKeyNoneMatchRequest->shouldReceive('setHeader')
      ->with(array('If-None-Match' => '*'))
      ->andReturn(m::self());
    $secondKeyNoneMatchRequest->shouldReceive('send')
      ->withNoArgs()
      ->andThrow(new \Guzzle\Http\Exception\ClientErrorResponseException());
    $httpClient->shouldReceive('put')
      ->with('first_collection/second_key', array('Content-Type'=>'application/json', 'If-None-Match'=>'"*"'), '{"name":"Bill"}')
      ->andReturn($secondKeyNoneMatchRequest);

    $postResponse = m::mock('Guzzle\Http\Message\Response');
    $postResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(true);
    $postResponse->shouldReceive('getHeader')
      ->with('ETag')
      ->andReturn('6966021b255bd0f7');
    $postResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(true);
    $postResponse->shouldReceive('getLocation')
      ->andReturn('/v0/first_collection/0727de1451204020/refs/6966021b255bd0f7');
    $postResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $postResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $postRequest = m::mock('Guzzle\Http\Message\Request');
    $postRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $postRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($postResponse);
    $httpClient->shouldReceive('post')
      ->with('first_collection', array('Content-Type'=>'application/json',), '{"name":"Adam"}')
      ->andReturn($postRequest);

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
    $godfatherResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(true);
    $godfatherResponse->shouldReceive('getHeader')
      ->with('ETag')
      ->andReturn('9c1bc18e60d93848');
    $godfatherResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(false);
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

    $previousRefResponse = m::mock('Guzzle\Http\Message\Response');
    $previousRefResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(true);
    $previousRefResponse->shouldReceive('getHeader')
      ->with('ETag')
      ->andReturn('741357981fd7b5cb');
    $previousRefResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(false);
    $previousRefResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"name":"John"}', true));
    $previousRefResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"name":"John"}');
    $previousRefRequest = m::mock('Guzzle\Http\Message\Request');
    $previousRefRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $previousRefRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($previousRefResponse);
    $httpClient->shouldReceive('get')
      ->with('first_collection/second_key/refs/741357981fd7b5cb')
      ->andReturn($previousRefRequest);

    $firstDeleteResponse = m::mock('Guzzle\Http\Message\Response');
    $firstDeleteResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $firstDeleteResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $firstDeleteResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(false);
    $firstDeleteResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $firstDeleteResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $firstDeleteRequest = m::mock('Guzzle\Http\Message\Request');
    $firstDeleteRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $firstDeleteRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($firstDeleteResponse);
    $httpClient->shouldReceive('delete')
      ->with('first_collection/first_key')
      ->andReturn($firstDeleteRequest);

    $secondDeleteResponse = m::mock('Guzzle\Http\Message\Response');
    $secondDeleteResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $secondDeleteResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $secondDeleteResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(false);
    $secondDeleteResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $secondDeleteResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $secondDeleteRequest = m::mock('Guzzle\Http\Message\Request');
    $secondDeleteRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $secondDeleteRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($secondDeleteResponse);
    $httpClient->shouldReceive('delete')
      ->with('first_collection/first_key?purge=true')
      ->andReturn($secondDeleteRequest);

    $defaultListResponse = m::mock('Guzzle\Http\Message\Response');
    $defaultListResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $defaultListResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(true);
    $defaultListResponse->shouldReceive('getHeader')
      ->with('Link')
      ->andReturn('</v0/films?limit=10&afterKey=the_godfather_part_2>; rel="next"');
    $defaultListResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(false);
    $defaultListResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count":10,"results":[{"path":{"collection":"films","key":"Pi","ref":"eb970a6a59d9c987"}}]}', true));
    $defaultListResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count":10,"results":[{"path":{"collection":"films","key":"Pi","ref":"eb970a6a59d9c987"}}]}');
    $defaultListRequest = m::mock('Guzzle\Http\Message\Request');
    $defaultListRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $defaultListRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($defaultListResponse);
    $httpClient->shouldReceive('get')
      ->with('films?limit=10')
      ->andReturn($defaultListRequest);

    $defaultListResponse = m::mock('Guzzle\Http\Message\Response');
    $defaultListResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $defaultListResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(true);
    $defaultListResponse->shouldReceive('getHeader')
      ->with('Link')
      ->andReturn('</v0/films?limit=5&afterKey=pulp_fiction>; rel="next"');
    $defaultListResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(false);
    $defaultListResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count":5,"results":[{"path":{"collection":"films","key":"Pi","ref":"eb970a6a59d9c987"}}]}', true));
    $defaultListResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count":5,"results":[{"path":{"collection":"films","key":"Pi","ref":"eb970a6a59d9c987"}}]}');
    $defaultListRequest = m::mock('Guzzle\Http\Message\Request');
    $defaultListRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $defaultListRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($defaultListResponse);
    $httpClient->shouldReceive('get')
      ->with('films?limit=5&startKey=anchorman')
      ->andReturn($defaultListRequest);

    $defaultListResponse = m::mock('Guzzle\Http\Message\Response');
    $defaultListResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $defaultListResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(true);
    $defaultListResponse->shouldReceive('getHeader')
      ->with('Link')
      ->andReturn('</v0/films?limit=5&afterKey=shawshank_redemption>; rel="next"');
    $defaultListResponse->shouldReceive('hasHeader')
      ->with('Location')
      ->andReturn(false);
    $defaultListResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count":5,"results":[{"path":{"collection":"films","key":"Pi","ref":"eb970a6a59d9c987"}}]}', true));
    $defaultListResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count":5,"results":[{"path":{"collection":"films","key":"Pi","ref":"eb970a6a59d9c987"}}]}');
    $defaultListRequest = m::mock('Guzzle\Http\Message\Request');
    $defaultListRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $defaultListRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($defaultListResponse);
    $httpClient->shouldReceive('get')
      ->with('films?limit=5&afterKey=anchorman')
      ->andReturn($defaultListRequest);

    $this->client = new Client('api-key', $httpClient);
  }

  public function tearDown()
  {
    m::close();
  }

  public function testPutWithCollection()
  {
    $kvPutOp = new KvPutOperation("first_collection", "second_key", json_encode(array("name" => "John")));
    $kvObject = $this->client->execute($kvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('741357981fd7b5cb', $kvObject->getRef());
  }

  public function testPutIfMatch()
  {
    $kvPutOp = new KvPutOperation("first_collection", "second_key", json_encode(array("name" => "Terry")), array('if-match' => '741357981fd7b5cb'));
    $kvObject = $this->client->execute($kvPutOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('0d1f15ab524a5c5a', $kvObject->getRef());
  }

  public function testPutIfNoneMatch()
  {
    $kvPutOp = new KvPutOperation("first_collection", "second_key", json_encode(array("name" => "Bill")), array('if-none-match' => '*'));
    $kvObject = $this->client->execute($kvPutOp);
    $this->assertNull($kvObject);
  }

  public function testPost()
  {
    $kvPostOp = new KvPostOperation("first_collection", json_encode(array("name" => "Adam")));
    $kvObject = $this->client->execute($kvPostOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertRegExp('/[a-f0-9]{16}/', $kvObject->getKey());
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

  public function testGetPreviousRef()
  {
    $kvFetchOp = new KvFetchOperation("first_collection", "second_key", "741357981fd7b5cb");
    $kvObject = $this->client->execute($kvFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvObject', $kvObject);
    $this->assertEquals('741357981fd7b5cb', $kvObject->getRef());
    $value = $kvObject->getValue();
    $this->assertArrayHasKey('name', $value);
    $this->assertEquals('John', $value['name']);
  }

  public function testDelete()
  {
    $kvDeleteOp = new KvDeleteOperation("first_collection", "first_key");
    $result = $this->client->execute($kvDeleteOp);
    $this->assertTrue($result);
  }

  public function testDeleteWithPurge()
  {
    $kvDeleteOp = new KvDeleteOperation("first_collection", "first_key", true);
    $result = $this->client->execute($kvDeleteOp);
    $this->assertTrue($result);
  }

  public function testListDefaultLimit()
  {
    $kvListOp = new KvListOperation("films", 10);
    $kvListObject = $this->client->execute($kvListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvListObject', $kvListObject);
    $this->assertEquals(10, $kvListObject->count());
    $this->assertEquals('/v0/films?limit=10&afterKey=the_godfather_part_2', $kvListObject->getLink());
  }

  public function testListInclusiveStartKey()
  {
    $kvListOp = new KvListOperation("films", 5, 'anchorman');
    $kvListObject = $this->client->execute($kvListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvListObject', $kvListObject);
    $this->assertEquals(5, $kvListObject->count());
    $this->assertEquals('/v0/films?limit=5&afterKey=pulp_fiction', $kvListObject->getLink());
  }

  public function testListExclusiveAfterKey()
  {
    $kvListOp = new KvListOperation("films", 5, null, 'anchorman');
    $kvListObject = $this->client->execute($kvListOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\KvListObject', $kvListObject);
    $this->assertEquals(5, $kvListObject->count());
    $this->assertEquals('/v0/films?limit=5&afterKey=shawshank_redemption', $kvListObject->getLink());
  }
}
