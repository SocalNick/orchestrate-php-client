<?php

namespace SocalNick\Orchestrate;

use GuzzleHttp;

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
  public function __construct($apiKey, $baseUri = 'https://api.orchestrate.io/v0/', GuzzleHttp\ClientInterface $httpClient = null)
  {
    $this->apiKey = $apiKey;

    if ($httpClient) {
      $this->httpClient = $httpClient;
    } else {
      $this->httpClient = new GuzzleHttp\Client([
        'base_uri' => $baseUri,
        'auth' => [$apiKey, ''],
      ]);
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
      if ($op instanceof DeleteOperationInterface) {
        $response = $this->httpClient->delete($op->getEndpoint(), [
          'headers' => $op->getHeaders(),
        ]);
      } elseif ($op instanceof PostOperationInterface) {
        $response = $this->httpClient->post($op->getEndpoint(), [
          'headers' => $op->getHeaders(),
          'body' => $op->getData(),
        ]);
      } elseif ($op instanceof PatchOperationInterface) {
        $response = $this->httpClient->patch($op->getEndpoint(), [
          'headers' => $op->getHeaders(),
          'body' => $op->getData(),
        ]);
      } elseif ($op instanceof PutOperationInterface) {
        $response = $this->httpClient->put($op->getEndpoint(), [
          'headers' => $op->getHeaders(),
          'body' => $op->getData(),
        ]);
      } else {
        $response = $this->httpClient->get($op->getEndpoint());
      }
    } catch (GuzzleHttp\Exception\ClientException $e) {
      throw new Exception\ClientException('ClientException occurred in request to Orchestrate: ' . $e->getMessage(), $e->getCode(), $e);
    } catch (GuzzleHttp\Exception\ServerException $e) {
      throw new Exception\ServerException('ServerException occurred in request to Orchestrate: ' . $e->getMessage(), $e->getCode(), $e);
    }

    $refLink = null;
    $location = null;

    if ($response->hasHeader('ETag')) {
      $refLink = str_replace('"', '', $response->getHeaderLine('ETag'));
    } elseif ($response->hasHeader('Link')) {
      $refLink = str_replace(
        ['<', '>; rel="next"'],
        ['', ''],
        $response->getHeaderLine('Link')
      );
    }

    if ($response->hasHeader('Location')) {
      $location = $response->getHeaderLine('Location');
    }
    $rawValue = $response->getBody(true);
    $value = json_decode($rawValue, true);

    return $op->getObjectFromResponse($refLink, $location, $value, $rawValue);
  }

}
