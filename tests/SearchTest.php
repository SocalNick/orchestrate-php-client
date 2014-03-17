<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\SearchOperation;
use SocalNick\Orchestrate\SearchResult;
use Mockery as m;

class SearchTest extends \PHPUnit_Framework_TestCase
{
  protected $client;

  protected function setUp()
  {
    $httpClient = m::mock('Guzzle\Http\ClientInterface');

    $defaultSearchResponse = m::mock('Guzzle\Http\Message\Response');
    $defaultSearchResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $defaultSearchResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $defaultSearchResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count":10,"total_count":12,"results": [{"path": {"collection": "films","key": "shawshank_redemption","ref": "6328a27142985690"}}]}', true));
    $defaultSearchResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count":10,"total_count":12,"results": [{"path": {"collection": "films","key": "shawshank_redemption","ref": "6328a27142985690"}}]}');
    $defaultSearchRequest = m::mock('Guzzle\Http\Message\Request');
    $defaultSearchRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $defaultSearchRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($defaultSearchResponse);
    $httpClient->shouldReceive('get')
      ->with('films/?query=%2A&limit=10&offset=0')
      ->andReturn($defaultSearchRequest);

    $searchResponse = m::mock('Guzzle\Http\Message\Response');
    $searchResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $searchResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $searchResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count":2,"total_count":8,"results":[{"path":{"collection":"films","key":"goodfellas","ref":"1cd244f81857f18a"}}]}', true));
    $searchResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count":2,"total_count":8,"results":[{"path":{"collection":"films","key":"goodfellas","ref":"1cd244f81857f18a"}}]}');
    $searchRequest = m::mock('Guzzle\Http\Message\Request');
    $searchRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $searchRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($searchResponse);
    $httpClient->shouldReceive('get')
      ->with('films/?query=Genre%3A%2ACrime%2A&limit=2&offset=2')
      ->andReturn($searchRequest);

    $this->client = new Client('api-key', $httpClient);
  }

  public function tearDown()
  {
    m::close();
  }

  public function testSearchDefaults()
  {
    $searchOp = new SearchOperation("films");
    $searchResult = $this->client->execute($searchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\SearchResult', $searchResult);
    $this->assertEquals(10, $searchResult->count());
    $this->assertEquals(12, $searchResult->totalCount());
  }

  public function testSearchWithQueryLimitOffset()
  {
    $searchOp = new SearchOperation("films", "Genre:*Crime*", 2, 2);
    $searchResult = $this->client->execute($searchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\SearchResult', $searchResult);
    $this->assertEquals(2, $searchResult->count());
    $this->assertEquals(8, $searchResult->totalCount());
  }

}
