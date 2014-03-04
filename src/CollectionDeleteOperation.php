<?php

namespace SocalNick\Orchestrate;

class CollectionDeleteOperation implements DeleteOperationInterface
{
  protected $collection;

  public function __construct($collection)
  {
    $this->collection = $collection;
  }

  public function getEndpoint()
  {
    return $this->collection . '?force=true';
  }

  public function getObjectFromResponse($ref, $value = null, $rawValue = null)
  {
    return true;
  }
}
