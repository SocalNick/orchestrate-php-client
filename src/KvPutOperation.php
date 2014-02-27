<?php

namespace SocalNick\Orchestrate;

class KvPutOperation extends KvFetchOperation implements PutOperationInterface
{
  protected $data;

  public function __construct($collection, $key, $data)
  {
    parent::__construct($collection, $key);
    $this->data = $data;
  }

  public function getData()
  {
    return $this->data;
  }

  public function getObjectFromResponse($ref, $value = null, $rawValue = null)
  {
    return new KvObject($this->collection, $this->key, $ref);
  }
}
