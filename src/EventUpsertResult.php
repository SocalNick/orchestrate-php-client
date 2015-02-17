<?php

namespace SocalNick\Orchestrate;

class EventUpsertResult
{
  protected $collection;
  protected $key;
  protected $type;
  protected $ref;
  protected $timestamp;
  protected $ordinal;

  public function __construct($collection, $key, $type, $ref, $timestamp, $ordinal)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->type = $type;
    $this->ref = $ref;
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

  public function getRef()
  {
    return $this->ref;
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
