<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
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
    $this->assertEquals('9113c836a4589e07', $searchResult->getValue()['results'][0]['path']['ref']);
  }

}
