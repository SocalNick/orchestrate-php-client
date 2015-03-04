<?php

namespace SocalNick\Orchestrate;

class KvListOperation implements OperationInterface
{
  protected $collection;
  protected $limit = 10;
  protected $startKey;
  protected $afterKey;
  protected $beforeKey;
  protected $endKey;

  public function __construct($collection, $limit = 10, $startKey = null, $afterKey = null, $beforeKey = null, $endKey = null)
  {
    $this->collection = $collection;
    $this->limit = $limit;
    if ($limit > 100) {
      trigger_error(sprintf('Invalid limit: %d. Maximum is 100', $limit));
      $limit = 100;
    }
    $this->startKey  = $startKey;
    $this->afterKey  = $afterKey;
    $this->beforeKey = $beforeKey;
    $this->endKey    = $endKey;
  }

  public function getEndpoint()
  {
    $queryParams = ['limit' => $this->limit];
    if ($this->startKey) {
      $queryParams['startKey'] = $this->startKey;
    } elseif ($this->afterKey) {
      $queryParams['afterKey'] = $this->afterKey;
    }

    if ($this->beforeKey) {
      $queryParams['beforeKey'] = $this->beforeKey;
    } elseif ($this->endKey) {
      $queryParams['endKey'] = $this->endKey;
    }
    
    return $this->collection . '?' . http_build_query($queryParams);
  }

  public function getObjectFromResponse($link, $location = null, $value = null, $rawValue = null)
  {
    return new KvListObject($this->collection, $link, $value, $rawValue);
  }
}
