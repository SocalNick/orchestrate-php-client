<?php

namespace SocalNick\Orchestrate;

class EventFetchOperation implements OperationInterface
{
  protected $collection;
  protected $key;
  protected $type;
  protected $start;
  protected $end;

  public function __construct($collection, $key, $type, $start = null, $end = null)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->type = $type;
    $this->start = $start;
    $this->end = $end;
  }

  public function getEndpoint()
  {
    $queryParams = $this->getQueryParams();

    return $this->collection  . '/' . $this->key . '/events/' . $this->type . (!empty($queryParams) ? '?' . http_build_query($queryParams) : '');
  }

  protected function getQueryParams()
  {
    $queryParams = array();
    if ($this->start) {
      $queryParams['start'] = $this->start;
    }
    if ($this->end) {
      $queryParams['end'] = $this->end;
    }

    return $queryParams;
  }

  public function getObjectFromResponse($ref, $value = null, $rawValue = null)
  {
    return new EventObject($this->collection, $this->key, $this->type, $value, $rawValue);
  }
}
