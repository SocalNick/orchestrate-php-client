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
      if ($op instanceof PutOperationInterface) {
        $request = $this->httpClient->put(
          $op->getEndpoint(),
          array(
            'Content-Type' => 'application/json',
          ),
          $op->getData()
        );
      } elseif ($op instanceof DeleteOperationInterface) {
        $request = $this->httpClient->delete($op->getEndpoint());
      } else {
        $request = $this->httpClient->get($op->getEndpoint());
      }

      $request->setAuth($this->apiKey);
      $response = $request->send();

    } catch (GuzzleHttp\Exception\BadResponseException $e) {
      // Client or server error
      return null;
    }

    $ref = str_replace('"', '', (string) $response->getHeader('ETag'));
    $value = $response->json();
    $rawValue = $response->getBody(true);

    return $op->getObjectFromResponse($ref, $value, $rawValue);
  }

}
