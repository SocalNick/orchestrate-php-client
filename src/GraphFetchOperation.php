<?php

namespace SocalNick\Orchestrate;

class GraphFetchOperation implements OperationInterface
{
  protected $collection;
  protected $key;
  protected $kind;

  public function __construct($collection, $key, $kind)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->kind = $kind;
  }

  public function getEndpoint()
  {
    return $this->collection  . '/' . $this->key . '/relations/' . $this->kind;
  }

  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    return new GraphObject($this->collection, $this->key, $this->kind, $value, $rawValue);
  }
}
