<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\EventFetchOperation;
use SocalNick\Orchestrate\EventPutOperation;
use SocalNick\Orchestrate\EventObject;
use Mockery as m;

class EventTest extends \PHPUnit_Framework_TestCase
{
  protected $client;
  protected static $now;

  public static function setUpBeforeClass()
  {
    self::$now = (int) microtime(true) * 1000;
  }

  protected function setUp()
  {
    $httpClient = m::mock('Guzzle\Http\ClientInterface');

    $defaultPutResponse = m::mock('Guzzle\Http\Message\Response');
    $defaultPutResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $defaultPutResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $defaultPutResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $defaultPutResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $defaultPutRequest = m::mock('Guzzle\Http\Message\Request');
    $defaultPutRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $defaultPutRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($defaultPutResponse);
    $httpClient->shouldReceive('put')
      ->with(
        'films/pulp_fiction/events/comment',
        array('Content-Type'=>'application/json'),
        '{"message":"This is my favorite movie!"}'
      )
      ->andReturn($defaultPutRequest);

    $timestampPutResponse = m::mock('Guzzle\Http\Message\Response');
    $timestampPutResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $timestampPutResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $timestampPutResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(array());
    $timestampPutResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('');
    $timestampPutRequest = m::mock('Guzzle\Http\Message\Request');
    $timestampPutRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $timestampPutRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($timestampPutResponse);
    $httpClient->shouldReceive('put')
      ->with(
        'films/pulp_fiction/events/comment?timestamp=' . self::$now,
        array('Content-Type'=>'application/json'),
        '{"message":"This is my favorite movie!"}'
      )
      ->andReturn($timestampPutRequest);

    $defaultGetResponse = m::mock('Guzzle\Http\Message\Response');
    $defaultGetResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $defaultGetResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $defaultGetResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count": 3,"results": [{"value": {"message": "This is my favorite movie!"},"timestamp": 1394330291000},{"value": {"message": "This is my favorite movie!"},"timestamp": 1394330191903},{"value": {"message": "This is my favorite movie!"},"timestamp": 1394330128930}]}', true));
    $defaultGetResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count": 3,"results": [{"value": {"message": "This is my favorite movie!"},"timestamp": 1394330291000},{"value": {"message": "This is my favorite movie!"},"timestamp": 1394330191903},{"value": {"message": "This is my favorite movie!"},"timestamp": 1394330128930}]}');
    $defaultGetRequest = m::mock('Guzzle\Http\Message\Request');
    $defaultGetRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $defaultGetRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($defaultGetResponse);
    $httpClient->shouldReceive('get')
      ->with('films/pulp_fiction/events/comment')
      ->andReturn($defaultGetRequest);

    $startEndGetResponse = m::mock('Guzzle\Http\Message\Response');
    $startEndGetResponse->shouldReceive('hasHeader')
      ->with('ETag')
      ->andReturn(false);
    $startEndGetResponse->shouldReceive('hasHeader')
      ->with('Link')
      ->andReturn(false);
    $startEndGetResponse->shouldReceive('json')
      ->withNoArgs()
      ->andReturn(json_decode('{"count": 1,"results": [{"value": {"message": "This is my favorite movie!"},"timestamp": ' . self::$now . '}]}', true));
    $startEndGetResponse->shouldReceive('getBody')
      ->with(true)
      ->andReturn('{"count": 1,"results": [{"value": {"message": "This is my favorite movie!"},"timestamp": ' . self::$now . '}]}');
    $startEndGetRequest = m::mock('Guzzle\Http\Message\Request');
    $startEndGetRequest->shouldReceive('setAuth')
      ->with('api-key')
      ->andReturn(m::self());
    $startEndGetRequest->shouldReceive('send')
      ->withNoArgs()
      ->andReturn($startEndGetResponse);
    $httpClient->shouldReceive('get')
      ->with('films/pulp_fiction/events/comment?start=' . self::$now . '&end=' . (self::$now+1))
      ->andReturn($startEndGetRequest);

    $this->client = new Client('api-key', $httpClient);
  }

  public function tearDown()
  {
    m::close();
  }

  public function testEventPutDefaultsToNow()
  {
    $evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(array("message" => "This is my favorite movie!")));
    $result = $this->client->execute($evPutOp);
    $this->assertTrue($result);
  }

  public function testEventPutWithTimestamp()
  {
    $evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(array("message" => "This is my favorite movie!")), self::$now);
    $result = $this->client->execute($evPutOp);
    $this->assertTrue($result);
  }

  public function testGet()
  {
    $evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment");
    $evObject = $this->client->execute($evFetchOp);
    $this->assertInstanceOf('SocalNick\Orchestrate\EventObject', $evObject);
    $this->assertGreaterThan(2, $evObject->count());
    $value = $evObject->getValue();
    $this->assertInternalType('array', $value);
    $this->assertArrayHasKey('results', $value);
    $results = $value['results'];
    $this->assertInternalType('array', $results);
  }

  public function testGetWithStartEnd()
  {
    $evGetOp = new EventFetchOperation("films", "pulp_fiction", "comment", self::$now, self::$now+1);
    $evObject = $this->client->execute($evGetOp);
    $this->assertEquals(1, $evObject->count());
    $value = $evObject->getValue();
    $results = $value['results'];
    $this->assertEquals(self::$now, $results[0]['timestamp']);
  }

}
