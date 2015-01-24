<?php

namespace SocalNick\Orchestrate;

class KvPostOperation extends KvPutOperation implements PostOperationInterface
{
  public function __construct($collection, $data)
  {
    $this->collection = $collection;
    $this->data = $data;
    $this->conditional = [];
  }

  public function getEndpoint()
  {
    return $this->collection;
  }
 
  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    $key = preg_replace('%/[^/]+/[^/]+/([^/]+)/[^/]+/[^/]+%s', '$1', $location);
    return new KvObject($this->collection, $key, $ref, $value);
  }
}
