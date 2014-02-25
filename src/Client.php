<?php

namespace SocalNick\Orchestrate;

use Guzzle\Http as GuzzleHttp;

/**
 * Orchestrate.io Client
 */
class Client
{

  /**
   * The API key used for authentication
   *
   * @var string
   */
  protected $apiKey = '';

  /**
   * The HTTP client used to send requests
   *
   * @var GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Constructor
   *
   * The API key is required.
   * If no HTTP client is passed, a Guzzle HTTP Client will be instantiated.
   *
   * @param string                     $apiKey
   * @param GuzzleHttp\ClientInterface $httpClient
   */
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

  /**
   * Execute an operaction
   *
   * A successful operation will return result as an array.
   * An unsuccessful operation will return null.
   *
   * @param  OperationInterface $op
   * @return array|null
   */
  public function execute(OperationInterface $op)
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
      // Client or server error
      return null;
    }

    return $response->json();
  }

}
