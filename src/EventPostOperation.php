<?php

namespace SocalNick\Orchestrate;

class EventPostOperation extends EventPutOperation implements PostOperationInterface
{
  public function __construct($collection, $key, $type, $data, $timestamp = null)
  {
    parent::__construct($collection, $key, $type, $data, $timestamp, null);
  }

  public function getEndpoint()
  {
    return $this->collection  . '/' . $this->key . '/events/' . $this->type . ($this->timestamp ? '/' . $this->timestamp : '');
  }
}
