<?php

namespace SocalNick\Orchestrate;

class EventDeleteOperation extends EventFetchOperation implements DeleteOperationInterface
{
  protected $purge;
  protected $ref;

  public function __construct($collection, $key, $type, $timestamp, $ordinal, $purge = false, $ref = null)
  {
    parent::__construct($collection, $key, $type, $timestamp, $ordinal);
    $this->purge = (bool) $purge;
    $this->ref = $ref;
  }

  public function getEndpoint()
  {
    return parent::getEndpoint() . ($this->purge ? '?purge=true' : '');
  }

  public function getHeaders()
  {
    $headers = [];

    if ($this->ref) {
      $headers['If-Match'] = "\"{$this->ref}\"";
    }

    return $headers;
  }

  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    return true;
  }
}

