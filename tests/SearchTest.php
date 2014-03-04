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
      ->andReturn(json_decode('{"count":10,"total_count":10,"results": [{"path": {"collection": "films","key": "shawshank_redemption","ref": "6328a27142985690"}}]}', true));
    $defaultSearchResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count":10,"total_count":10,"results": [{"path": {"collection": "films","key": "shawshank_redemption","ref": "6328a27142985690"}}]}');
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
    $this->assertEquals(10, $searchResult->totalCount());
  }

}
