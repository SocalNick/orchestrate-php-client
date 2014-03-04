<?php

namespace SocalNick\Orchestrate;

class KvListObject
{
  protected $collection;
  protected $link;
  protected $value;
  protected $rawValue;

  public function __construct($collection, $link, $value = null, $rawValue = null)
  {
    $this->collection = $collection;
    $this->link = $link;
    $this->value = $value;
    $this->rawValue = $rawValue;
  }

  public function getCollection()
  {
    return $this->collection;
  }

  public function getLink()
  {
    return $this->link;
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
}
