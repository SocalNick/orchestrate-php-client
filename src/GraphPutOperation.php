<?php

namespace SocalNick\Orchestrate;

class GraphPutOperation extends GraphFetchOperation implements PutOperationInterface
{
  protected $toCollection;
  protected $toKey;

  public function __construct($collection, $key, $kind, $toCollection, $toKey)
  {
    parent::__construct($collection, $key, $kind);
    $this->toCollection = $toCollection;
    $this->toKey = $toKey;
  }

  public function getEndpoint()
  {
    return $this->collection  . '/' . $this->key . '/relation/' . $this->kind . '/' . $this->toCollection . '/' . $this->toKey;
  }

  public function getHeaders()
  {
    return null;
  }

  public function getData()
  {
    return null;
  }

  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    return true;
  }
}
