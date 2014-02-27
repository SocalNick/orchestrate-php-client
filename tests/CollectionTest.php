<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\KvFetchOperation;
use SocalNick\Orchestrate\KvPutOperation;
use Mockery as m;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

    protected function setUp()
    {
        $httpClient = m::mock('Guzzle\Http\ClientInterface');

        $firstKeyResponse = m::mock('Guzzle\Http\Message\Response');
        $firstKeyResponse->shouldReceive('getStatusCode')
            ->withNoArgs()
            ->andReturn(201);
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
        $godfatherResponse->shouldReceive('getStatusCode')
            ->withNoArgs()
            ->andReturn(200);
        $godfatherResponse->shouldReceive('json')
            ->withNoArgs()
            ->andReturn(json_decode('{"Title": "The Godfather","Released": "24 Mar 1972"}', true));
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
        $result = $this->client->execute($kvPutOp);
        $this->assertTrue($result);
    }

    public function testKeyDoesNotExist404()
    {
        $kvFetchOp = new KvFetchOperation("first_collection", "missing_key");
        $result = $this->client->execute($kvFetchOp);
        $this->assertNull($result);
    }

    public function testGet()
    {
        $kvFetchOp = new KvFetchOperation("films", "the_godfather");
        $result = $this->client->execute($kvFetchOp);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('Title', $result);
        $this->assertArrayHasKey('Released', $result);
        $this->assertEquals('The Godfather', $result['Title']);
        $this->assertEquals('24 Mar 1972', $result['Released']);
    }
}
