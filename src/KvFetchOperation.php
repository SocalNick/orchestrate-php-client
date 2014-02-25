<?php

namespace SocalNick\Orchestrate;

class KvFetchOperation implements Operation
{
  protected $collection;
  protected $key;

  public function __construct($collection, $key)
  {
    $this->collection = $collection;
    $this->key = $key;
  }

  public function getCollection()
  {
    return $this->collection;
  }

  public function getKey()
  {
    return $this->key;
  }
}
