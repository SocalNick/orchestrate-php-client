<?php

namespace SocalNick\Orchestrate;

class EventPutOperation implements PutOperationInterface
{
  protected $collection;
  protected $key;
  protected $type;
  protected $data;
  protected $timestamp;
  protected $ordinal;
  protected $ref;

  public function __construct($collection, $key, $type, $data, $timestamp, $ordinal, $ref = null)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->type = $type;
    $this->data = $data;
    $this->timestamp = $timestamp;
    $this->ordinal = $ordinal;
    $this->ref = $ref;
  }

  public function getEndpoint()
  {
    return $this->collection  . '/' . $this->key . '/events/' . $this->type . '/' . $this->timestamp . '/' . $this->ordinal;
  }

  public function getData()
  {
    return $this->data;
  }

  public function getHeaders()
  {
    $headers = [
      'Content-Type' => 'application/json',
    ];

    if ($this->ref) {
      $headers['If-Match'] = "\"{$this->ref}\"";
    }

    return $headers;
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
    return new EventUpsertResult($this->collection, $this->key, $this->type, $ref, $timestamp, $ordinal);
  }
}
