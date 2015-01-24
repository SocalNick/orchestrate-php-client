<?php

namespace SocalNick\Orchestrate;

class KvListOperation implements OperationInterface
{
  protected $collection;
  protected $limit = 10;
  protected $startKey;
  protected $afterKey;

  public function __construct($collection, $limit = 10, $startKey = null, $afterKey = null)
  {
    $this->collection = $collection;
    $this->limit = $limit;
    if ($limit > 100) {
      trigger_error(sprintf('Invalid limit: %d. Maximum is 100', $limit));
      $limit = 100;
    }
    $this->startKey = $startKey;
    $this->afterKey = $afterKey;
  }

  public function getEndpoint()
  {
    $queryParams = ['limit' => $this->limit];
    if ($this->startKey) {
      $queryParams['startKey'] = $this->startKey;
    } elseif ($this->afterKey) {
      $queryParams['afterKey'] = $this->afterKey;
    }

    return $this->collection . '?' . http_build_query($queryParams);
  }

  public function getObjectFromResponse($link, $location = null, $value = null, $rawValue = null)
  {
    return new KvListObject($this->collection, $link, $value, $rawValue);
  }
}
