<?php

namespace SocalNick\Orchestrate;

class GraphObject
{
  protected $collection;
  protected $key;
  protected $kind;
  protected $value;
  protected $rawValue;

  public function __construct($collection, $key, $kind, $value = null, $rawValue = null)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->kind = $kind;
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

  public function getKind()
  {
    return $this->kind;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function getRawValue()
  {
    return $this->rawValue;
  }

  public function getNext()
  {
    if (isset($this->value['next'])) {
      return $this->value['next'];
    }
  }

  public function getPrev()
  {
    if (isset($this->value['prev'])) {
      return $this->value['prev'];
    }
  }

  public function count()
  {
    if (isset($this->value['count'])) {
      return $this->value['count'];
    }
  }
}
