<?php

namespace SocalNick\Orchestrate;

class KvFetchOperation implements OperationInterface
{
  protected $collection;
  protected $key;

  public function __construct($collection, $key)
  {
    $this->collection = $collection;
    $this->key = $key;
  }

  public function getEndpoint()
  {
    return $this->collection  . '/' . $this->key;
  }

  public function getObjectFromResponse($ref, $value = null, $rawValue = null)
  {
    return new KvObject($this->collection, $this->key, $ref, $value, $rawValue);
  }
}
