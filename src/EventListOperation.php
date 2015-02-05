<?php

namespace SocalNick\Orchestrate;

class EventListOperation implements OperationInterface
{
  protected $collection;
  protected $key;
  protected $type;
  protected $limit;
  protected $startEvent;
  protected $afterEvent;
  protected $beforeEvent;
  protected $endEvent;

  public function __construct($collection, $key, $type, $limit = 10, $startEvent = null, $afterEvent = null, $beforeEvent = null, $endEvent = null)
  {
    $this->collection = $collection;
    $this->key = $key;
    $this->type = $type;
    $this->limit = $limit;
    if ($limit > 100) {
      trigger_error(sprintf('Invalid limit: %d. Maximum is 100', $limit));
      $limit = 100;
    }
    $this->startEvent = $startEvent;
    $this->afterEvent = $afterEvent;
    $this->beforeEvent = $beforeEvent;
    $this->endEvent = $endEvent;
  }

  public function getEndpoint()
  {
    $queryParams = $this->getQueryParams();

    return $this->collection  . '/' . $this->key . '/events/' . $this->type . (!empty($queryParams) ? '?' . http_build_query($queryParams) : '');
  }

  protected function getQueryParams()
  {
    $queryParams = ['limit' => $this->limit];
    if ($this->startEvent) {
      $queryParams['startEvent'] = $this->startEvent;
    }
    if ($this->afterEvent) {
      $queryParams['afterEvent'] = $this->afterEvent;
    }
    if ($this->beforeEvent) {
      $queryParams['beforeEvent'] = $this->beforeEvent;
    }
    if ($this->endEvent) {
      $queryParams['endEvent'] = $this->endEvent;
    }

    return $queryParams;
  }

  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    return new EventListObject($this->collection, $this->key, $this->type, $value, $rawValue);
  }
}
