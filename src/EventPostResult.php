<?php

namespace SocalNick\Orchestrate;

class EventPostResult
{
  protected $collection;
  protected $key;
  protected $type;
  protected $timestamp;
  protected $ordinal;

  public function __construct($collection, $key, $type, $timestamp, $ordinal)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->type = $type;
    $this->timestamp = $timestamp;
    $this->ordinal = $ordinal;
  }

  public function getCollection()
  {
    return $this->collection;
  }

  public function getKey()
  {
    return $this->key;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getTimestamp()
  {
    return $this->timestamp;
  }

  public function getOrdinal()
  {
    return $this->ordinal;
  }
}
