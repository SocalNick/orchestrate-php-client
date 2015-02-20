<?php

namespace SocalNick\Orchestrate;

class EventListObject
{
  protected $collection;
  protected $key;
  protected $type;
  protected $value;
  protected $rawValue;

  public function __construct($collection, $key, $type, $value = null, $rawValue = null)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->type = $type;
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

  public function getType()
  {
    return $this->type;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function getRawValue()
  {
    return $this->rawValue;
  }

  public function count()
  {
    if (isset($this->value['count'])) {
      return $this->value['count'];
    }
  }

  public function getNext()
  {
    if (isset($this->value['next'])) {
      return $this->value['next'];
    }
  }
}
