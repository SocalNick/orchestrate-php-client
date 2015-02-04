<?php

namespace SocalNick\Orchestrate;

class SearchResult
{
  protected $collection;
  protected $value;
  protected $rawValue;

  public function __construct($collection, $value = null, $rawValue = null)
  {
    $this->collection = $collection;
    $this->value = $value;
    $this->rawValue = $rawValue;
  }

  public function getCollection()
  {
    return $this->collection;
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

  public function totalCount()
  {
    if (isset($this->value['total_count'])) {
      return $this->value['total_count'];
    }
  }
}
