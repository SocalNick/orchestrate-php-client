<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\GraphFetchOperation;
use SocalNick\Orchestrate\GraphPutOperation;
use SocalNick\Orchestrate\GraphDeleteOperation;
use SocalNick\Orchestrate\GraphObject;
use Mockery as m;

class GraphTest extends \PHPUnit_Framework_TestCase
{
  protected $client;

  protected function setUp()
  {
    $httpClient = m::mock('Guzzle\Http\ClientInterface');

    $firstPutResponse = m::mock('Guzzle\Http\Message\Response');
    $firstPutResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $firstPutResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $firstPutResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $firstPutResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $firstPutRequest = m::mock('Guzzle\Http\Message\Request');
    $firstPutRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $firstPutRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($firstPutResponse);
    $httpClient->shouldReceive('put')
      ->with(
        'films/the_godfather/relation/sequel/films/the_godfather_part_2',
        null,
        null
      )
      ->andReturn($firstPutRequest);

    $secondPutResponse = m::mock('Guzzle\Http\Message\Response');
    $secondPutResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $secondPutResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $secondPutResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $secondPutResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $secondPutRequest = m::mock('Guzzle\Http\Message\Request');
    $secondPutRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $secondPutRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($secondPutResponse);
    $httpClient->shouldReceive('put')
      ->with(
        'films/the_godfather_part_2/relation/sequel/films/the_godfather_part_3',
        null,
        null
      )
      ->andReturn($secondPutRequest);

    $getResponse = m::mock('Guzzle\Http\Message\Response');
    $getResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $getResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $getResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count": 1,"results": [{"path": {"collection": "films","key": "the_godfather_part_3","ref": "4551215def111439"},"value": {"Title": "The Godfather: Part III","Year": 1990}}]}', true));
    $getResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count": 1,"results": [{"path": {"collection": "films","key": "the_godfather_part_3","ref": "4551215def111439"},"value": {"Title": "The Godfather: Part III","Year": 1990}}]}');
    $getRequest = m::mock('Guzzle\Http\Message\Request');
    $getRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $getRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($getResponse);
    $httpClient->shouldReceive('get')
      ->with('films/the_godfather/relations/sequel/sequel?limit=10&offset=0')
      ->andReturn($getRequest);

    $firstDeleteResponse = m::mock('Guzzle\Http\Message\Response');
    $firstDeleteResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $firstDeleteResponse->shouldReceive('hasHeader')
      ->with('Link')
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
      ->with('films/the_godfather/relation/sequel/films/the_godfather_part_2?purge=true')
      ->andReturn($firstDeleteRequest);

    $secondDeleteResponse = m::mock('Guzzle\Http\Message\Response');
    $secondDeleteResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $secondDeleteResponse->shouldReceive('hasHeader')
      ->with('Link')
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
      ->with('films/the_godfather_part_2/relation/sequel/films/the_godfather_part_3?purge=true')
      ->andReturn($secondDeleteRequest);

    $getResponse = m::mock('Guzzle\Http\Message\Response');
    $getResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $getResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $getResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count": 0,"results": []}', true));
    $getResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count": 0,"results": []}');
    $getRequest = m::mock('Guzzle\Http\Message\Request');
    $getRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $getRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($getResponse);
    $httpClient->shouldReceive('get')
      ->with('films/the_godfather/relations/sequel?limit=10&offset=0')
      ->andReturn($getRequest);

    $getResponse = m::mock('Guzzle\Http\Message\Response');
    $getResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $getResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $getResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count": 0,"results": []}', true));
    $getResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count": 0,"results": []}');
    $getRequest = m::mock('Guzzle\Http\Message\Request');
    $getRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $getRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($getResponse);
    $httpClient->shouldReceive('get')
      ->with('films/the_godfather_part_2/relations/sequel?limit=10&offset=0')
      ->andReturn($getRequest);

    $this->client = new Client('api-key', $httpClient);
  }

  public function tearDown()
  {
    m::close();
  }

  public function testPut()
  {
    $graphPutOp = new GraphPutOperation("films", "the_godfather", "sequel", "films", "the_godfather_part_2");
    $result = $this->client->execute($graphPutOp);
    $this->assertTrue($result);

    $graphPutOp = new GraphPutOperation("films", "the_godfather_part_2", "sequel", "films", "the_godfather_part_3");
    $result = $this->client->execute($graphPutOp);
    $this->assertTrue($result);
  }

  public function testGet()
  {
    $graphFetchOp = new GraphFetchOperation("films", "the_godfather", "sequel/sequel");
    $graphObject = $this->client->execute($graphFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\GraphObject', $graphObject);
    $this->assertEquals(1, $graphObject->count());
    $value = $graphObject->getValue();
    $this->assertInternalType('array', $value);
    $this->assertArrayHasKey('results', $value);
    $results = $value['results'];
    $this->assertInternalType('array', $results);
    $this->assertArrayHasKey('path', $results[0]);
    $this->assertArrayHasKey('value', $results[0]);
  }

  public function testDelete()
  {
    $graphDeleteOp = new GraphDeleteOperation("films", "the_godfather", "sequel", "films", "the_godfather_part_2");
    $result = $this->client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphDeleteOp = new GraphDeleteOperation("films", "the_godfather_part_2", "sequel", "films", "the_godfather_part_3");
    $result = $this->client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphFetchOp = new GraphFetchOperation("films", "the_godfather", "sequel");
    $graphObject = $this->client->execute($graphFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\GraphObject', $graphObject);
    $this->assertEquals(0, $graphObject->count());

    $graphFetchOp = new GraphFetchOperation("films", "the_godfather_part_2", "sequel");
    $graphObject = $this->client->execute($graphFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\GraphObject', $graphObject);
    $this->assertEquals(0, $graphObject->count());
  }

}
