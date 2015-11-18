<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\SearchAggregateOperation;
use SocalNick\Orchestrate\SearchOperation;
use SocalNick\Orchestrate\SearchResult;

class SearchTest extends \PHPUnit_Framework_TestCase
{
  protected static $client;

  public static function setUpBeforeClass()
  {
    self::$client = new Client(getenv('ORCHESTRATE_API_KEY'));
  }

  public function testSearchDefaults()
  {
    $searchOp = new SearchOperation("films");
    $searchResult = self::$client->execute($searchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\SearchResult', $searchResult);
    $this->assertEquals(10, $searchResult->count());
    $this->assertEquals(12, $searchResult->totalCount());
  }

  public function testSearchWithQueryLimitOffsetSort()
  {
    $searchOp = new SearchOperation("films", "Genre:*Crime*", 2, 2, 'value.Title:asc');
    $searchResult = self::$client->execute($searchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\SearchResult', $searchResult);
    $this->assertEquals(2, $searchResult->count());
    $this->assertEquals(8, $searchResult->totalCount());
    $this->assertEquals('c7a422a18e146792', $searchResult->getValue()['results'][0]['path']['ref']);
    $this->assertEquals('/v0/films/?offset=4&query=Genre%3A%2ACrime%2A&limit=2&sort=value.Title%3Aasc', $searchResult->getNext());
    $this->assertEquals('/v0/films/?offset=0&query=Genre%3A%2ACrime%2A&limit=2&sort=value.Title%3Aasc', $searchResult->getPrev());
  }

  public function testSearchWithAggregates()
  {
    $searchOp = new SearchAggregateOperation("films", 'value.imdbRating:stats');
    $searchResult = self::$client->execute($searchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\SearchResult', $searchResult);
    $this->assertEquals(5.9, $searchResult->getValue()['aggregates'][0]['statistics']['min']);
    $this->assertEquals(9.3, $searchResult->getValue()['aggregates'][0]['statistics']['max']);
  }
}
