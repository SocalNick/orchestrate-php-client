<?php

namespace SocalNick\Orchestrate\Tests;

use SocalNick\Orchestrate\Client;
use SocalNick\Orchestrate\KvFetchOperation;
use Mockery as m;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

    protected function setUp()
    {
        $httpClient = m::mock('Guzzle\Http\ClientInterface');

        $missingRequest = m::mock('Guzzle\Http\Message\Request');
        $missingRequest->shouldReceive('send')
            ->withNoArgs()
            ->andThrow(new \Guzzle\Http\Exception\ClientErrorResponseException());
        $httpClient->shouldReceive('get')
            ->with('first_collection/missing_key', array(), array('auth' => array('api-key')))
            ->andReturn($missingRequest);
        $this->client = new Client('api-key', $httpClient);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testKeyDoesNotExist404()
    {
        $kvFetchOp = new KvFetchOperation("first_collection", "missing_key");
        $result = $this->client->execute($kvFetchOp);
        $this->assertNull($result);
    }
}
