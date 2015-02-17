<?php

namespace SocalNick\Orchestrate;

class EventPostOperation implements PostOperationInterface
{
  protected $collection;
  protected $key;
  protected $type;
  protected $data;
  protected $timestamp;

  public function __construct($collection, $key, $type, $data, $timestamp = null)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->type = $type;
    $this->data = $data;
    $this->timestamp = $timestamp;
  }

  public function getEndpoint()
  {
    return $this->collection  . '/' . $this->key . '/events/' . $this->type . ($this->timestamp ? '/' . $this->timestamp : '');
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
    $matches = [];
    $preg_return = preg_match("%/v0/{$this->collection}/{$this->key}/events/{$this->type}/([^/]+)/([^/]+)%s", $location, $matches);
    if (!$preg_return) {
      return null;
    }
    $timestamp = $matches[1];
    $ordinal = $matches[2];
    return new EventPostResult($this->collection, $this->key, $this->type, $timestamp, $ordinal);
  }
}
