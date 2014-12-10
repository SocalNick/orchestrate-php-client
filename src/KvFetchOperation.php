<?php

namespace SocalNick\Orchestrate;

class KvFetchOperation implements OperationInterface
{
  protected $collection;
  protected $key;
  protected $ref;

  public function __construct($collection, $key, $ref = null)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->ref = $ref;
  }

  public function getEndpoint()
  {
    return $this->collection  . '/' . $this->key . ($this->ref ? '/refs/' . $this->ref : '');
  }

  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    return new KvObject($this->collection, $this->key, $ref, $value, $rawValue);
  }
}
