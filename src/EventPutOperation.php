<?php

namespace SocalNick\Orchestrate;

class EventPutOperation extends EventFetchOperation implements PutOperationInterface
{
  protected $data;
  protected $timestamp;

  public function __construct($collection, $key, $type, $data, $timestamp = null)
  {
    parent::__construct($collection, $key, $type);
    $this->data = $data;
    $this->timestamp = $timestamp;
  }

  protected function getQueryParams()
  {
    $queryParams = [];
    if ($this->timestamp) {
      $queryParams['timestamp'] = $this->timestamp;
    }

    return $queryParams;
  }

  public function getHeaders()
  {
    $headers = [
      'Content-Type' => 'application/json',
    ];

    return $headers;
  }

  public function getData()
  {
    return $this->data;
  }

  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    return true;
  }
}
