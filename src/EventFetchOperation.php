<?php

namespace SocalNick\Orchestrate;

class EventFetchOperation implements OperationInterface
{
  protected $collection;
  protected $key;
  protected $type;
  protected $timestamp;
  protected $ordinal;

  public function __construct($collection, $key, $type, $timestamp, $ordinal)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->type = $type;
    $this->timestamp = $timestamp;
    $this->ordinal = $ordinal;
  }

  public function getEndpoint()
  {
    return $this->collection  . '/' . $this->key . '/events/' . $this->type . '/' . $this->timestamp . '/' . $this->ordinal;
  }

  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    return new EventObject($this->collection, $this->key, $this->type, $ref, $value, $rawValue);
  }
}
