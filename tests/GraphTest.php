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

    $thirdPutResponse = m::mock('Guzzle\Http\Message\Response');
    $thirdPutResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $thirdPutResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $thirdPutResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $thirdPutResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $thirdPutRequest = m::mock('Guzzle\Http\Message\Request');
    $thirdPutRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $thirdPutRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($thirdPutResponse);
    $httpClient->shouldReceive('put')
      ->with(
        'directors/francis_ford_coppola/relation/films_directed/films/the_godfather',
        null,
        null
      )
      ->andReturn($thirdPutRequest);

    $fourthPutResponse = m::mock('Guzzle\Http\Message\Response');
    $fourthPutResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $fourthPutResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $fourthPutResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $fourthPutResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $fourthPutRequest = m::mock('Guzzle\Http\Message\Request');
    $fourthPutRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $fourthPutRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($fourthPutResponse);
    $httpClient->shouldReceive('put')
      ->with(
        'directors/francis_ford_coppola/relation/films_directed/films/the_godfather_part_2',
        null,
        null
      )
      ->andReturn($fourthPutRequest);

    $fifthPutResponse = m::mock('Guzzle\Http\Message\Response');
    $fifthPutResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $fifthPutResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $fifthPutResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $fifthPutResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $fifthPutRequest = m::mock('Guzzle\Http\Message\Request');
    $fifthPutRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $fifthPutRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($fifthPutResponse);
    $httpClient->shouldReceive('put')
      ->with(
        'directors/francis_ford_coppola/relation/films_directed/films/the_godfather_part_3',
        null,
        null
      )
      ->andReturn($fifthPutRequest);

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
    
    $getResponse = m::mock('Guzzle\Http\Message\Response');
    $getResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $getResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $getResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count":3,"results":[{"path":{"collection":"films","key":"the_godfather","ref":"9c1bc18e60d93848","reftime":1393300944033},"value":{"Title":"The Godfather"},"reftime":1393300944033},{"path":{"collection":"films","key":"the_godfather_part_2","ref":"dd76dd28ff14d348","reftime":1395020752366},"value":{"Title":"The Godfather: Part II"},"reftime":1395020752366},{"path":{"collection":"films","key":"the_godfather_part_3","ref":"4551215def111439","reftime":1395020779717},"value":{"Title":"The Godfather: Part III"},"reftime":1395020779717}]}', true));
    $getResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count":3,"results":[{"path":{"collection":"films","key":"the_godfather","ref":"9c1bc18e60d93848","reftime":1393300944033},"value":{"Title":"The Godfather"},"reftime":1393300944033},{"path":{"collection":"films","key":"the_godfather_part_2","ref":"dd76dd28ff14d348","reftime":1395020752366},"value":{"Title":"The Godfather: Part II"},"reftime":1395020752366},{"path":{"collection":"films","key":"the_godfather_part_3","ref":"4551215def111439","reftime":1395020779717},"value":{"Title":"The Godfather: Part III"},"reftime":1395020779717}]}');
    $getRequest = m::mock('Guzzle\Http\Message\Request');
    $getRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $getRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($getResponse);
    $httpClient->shouldReceive('get')
      ->with('directors/francis_ford_coppola/relations/films_directed?limit=3&offset=0')
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
      ->andReturn(json_decode('{"count":1,"results":[{"path":{"collection":"films","key":"the_godfather_part_2","ref":"dd76dd28ff14d348","reftime":1395020752366},"value":{"Title":"The Godfather: Part II"},"reftime":1395020752366}],"next":"/v0/directors/francis_ford_coppola/relations/films_directed?limit=1&offset=2","prev":"/v0/directors/francis_ford_coppola/relations/films_directed?limit=1&offset=0"}', true));
    $getResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count":1,"results":[{"path":{"collection":"films","key":"the_godfather_part_2","ref":"dd76dd28ff14d348","reftime":1395020752366},"value":{"Title":"The Godfather: Part II"},"reftime":1395020752366}],"next":"/v0/directors/francis_ford_coppola/relations/films_directed?limit=1&offset=2","prev":"/v0/directors/francis_ford_coppola/relations/films_directed?limit=1&offset=0"}');
    $getRequest = m::mock('Guzzle\Http\Message\Request');
    $getRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $getRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($getResponse);
    $httpClient->shouldReceive('get')
      ->with('directors/francis_ford_coppola/relations/films_directed?limit=1&offset=1')
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

    $thirdDeleteResponse = m::mock('Guzzle\Http\Message\Response');
    $thirdDeleteResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $thirdDeleteResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $thirdDeleteResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $thirdDeleteResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $thirdDeleteRequest = m::mock('Guzzle\Http\Message\Request');
    $thirdDeleteRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $thirdDeleteRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($thirdDeleteResponse);
    $httpClient->shouldReceive('delete')
      ->with('directors/francis_ford_coppola/relation/films_directed/films/the_godfather?purge=true')
      ->andReturn($thirdDeleteRequest);

    $fourthDeleteResponse = m::mock('Guzzle\Http\Message\Response');
    $fourthDeleteResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $fourthDeleteResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $fourthDeleteResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $fourthDeleteResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $fourthDeleteRequest = m::mock('Guzzle\Http\Message\Request');
    $fourthDeleteRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $fourthDeleteRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($fourthDeleteResponse);
    $httpClient->shouldReceive('delete')
      ->with('directors/francis_ford_coppola/relation/films_directed/films/the_godfather_part_2?purge=true')
      ->andReturn($fourthDeleteRequest);

    $fifthDeleteResponse = m::mock('Guzzle\Http\Message\Response');
    $fifthDeleteResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $fifthDeleteResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $fifthDeleteResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $fifthDeleteResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $fifthDeleteRequest = m::mock('Guzzle\Http\Message\Request');
    $fifthDeleteRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $fifthDeleteRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($fifthDeleteResponse);
    $httpClient->shouldReceive('delete')
      ->with('directors/francis_ford_coppola/relation/films_directed/films/the_godfather_part_3?purge=true')
      ->andReturn($fifthDeleteRequest);

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
      ->with('directors/francis_ford_coppola/relations/films_directed?limit=10&offset=0')
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

    $graphPutOp = new GraphPutOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather");
    $result = $this->client->execute($graphPutOp);
    $this->assertTrue($result);

    $graphPutOp = new GraphPutOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather_part_2");
    $result = $this->client->execute($graphPutOp);
    $this->assertTrue($result);

    $graphPutOp = new GraphPutOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather_part_3");
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

  public function testGetWithLimitOffset()
  {
    $graphFetchOp = new GraphFetchOperation("directors", "francis_ford_coppola", "films_directed", 3, 0);
    $graphObject = $this->client->execute($graphFetchOp);
    $this->assertEquals(3, $graphObject->count());

    $graphFetchOp = new GraphFetchOperation("directors", "francis_ford_coppola", "films_directed", 1, 1);
    $graphObject = $this->client->execute($graphFetchOp);
    $this->assertEquals(1, $graphObject->count());
    $this->assertEquals('/v0/directors/francis_ford_coppola/relations/films_directed?limit=1&offset=2', $graphObject->getNext());
    $this->assertEquals('/v0/directors/francis_ford_coppola/relations/films_directed?limit=1&offset=0', $graphObject->getPrev());
  }

  public function testDelete()
  {
    $graphDeleteOp = new GraphDeleteOperation("films", "the_godfather", "sequel", "films", "the_godfather_part_2");
    $result = $this->client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphDeleteOp = new GraphDeleteOperation("films", "the_godfather_part_2", "sequel", "films", "the_godfather_part_3");
    $result = $this->client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphDeleteOp = new GraphDeleteOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather");
    $result = $this->client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphDeleteOp = new GraphDeleteOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather_part_2");
    $result = $this->client->execute($graphDeleteOp);
    $this->assertTrue($result);

    $graphDeleteOp = new GraphDeleteOperation("directors", "francis_ford_coppola", "films_directed", "films", "the_godfather_part_3");
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

    $graphFetchOp = new GraphFetchOperation("directors", "francis_ford_coppola", "films_directed");
    $graphObject = $this->client->execute($graphFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\GraphObject', $graphObject);
    $this->assertEquals(0, $graphObject->count());
  }

}
