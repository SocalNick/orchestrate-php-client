<?php

namespace SocalNick\Orchestrate;

use Guzzle\Http as GuzzleHttp;

class Client
{

  protected $apiKey;
  protected $httpClient;

  public function __construct($apiKey, GuzzleHttp\ClientInterface $httpClient = null)
  {
    $this->apiKey = $apiKey;

    if ($httpClient) {
      $this->httpClient = $httpClient;
    } else {
      $this->httpClient = new GuzzleHttp\Client(
        'https://api.orchestrate.io/{version}',
        array(
          'version' => 'v0',
        )
      );
    }
  }

  public function execute(Operation $op)
  {
    try {
      $response = $this->httpClient->get(
        $op->getCollection() . '/' . $op->getKey(),
        array(),
        array(
          'auth' => array($this->apiKey),
        )
      )->send();
    } catch (GuzzleHttp\Exception\BadResponseException $e) {
      return null;
    }

    return $response->json();
  }

}
