<?php

namespace SocalNick\Orchestrate;

class KvDeleteOperation extends KvFetchOperation implements DeleteOperationInterface
{
  protected $purge;

  public function __construct($collection, $key, $purge = false)
  {
    parent::__construct($collection, $key);
    $this->purge = (bool) $purge;
  }

  public function getEndpoint()
  {
    return parent::getEndpoint() . ($this->purge ? '?purge=true' : '');
  }

  public function getHeaders()
  {
    return [];
  }

  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    return true;
  }
}
