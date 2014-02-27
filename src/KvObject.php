<?php

namespace SocalNick\Orchestrate;

class KvObject
{
  protected $collection;
  protected $key;
  protected $ref;
  protected $value;
  protected $rawValue;

  public function __construct($collection, $key, $ref, $value = null, $rawValue = null)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->ref = $ref;
    $this->value = $value;
    $this->rawValue = $rawValue;
  }

  public function getCollection()
  {
    return $this->collection;
  }

  public function getKey()
  {
    return $this->key;
  }

  public function getRef()
  {
    return $this->ref;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function getRawValue()
  {
    return $this->rawValue;
  }
}
