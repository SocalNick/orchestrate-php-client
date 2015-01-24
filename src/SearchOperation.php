<?php

namespace SocalNick\Orchestrate;

class SearchOperation implements OperationInterface
{
  protected $collection;
  protected $query = '*';
  protected $limit = 10;
  protected $offset = 0;
  protected $sort = null;

  public function __construct($collection, $query = '*', $limit = 10, $offset = 0, $sort = null)
  {
    $this->collection = $collection;
    $this->query = $query;
    $this->limit = $limit;
    $this->sort = $sort;
    if ($limit > 100) {
      trigger_error(sprintf('Invalid limit: %d. Maximum is 100', $limit));
      $limit = 100;
    }
    $this->offset = $offset;
  }

  public function getEndpoint()
  {
    $queryParams = [
      'query' => $this->query,
      'limit' => $this->limit,
      'offset' => $this->offset,
      'sort' => $this->sort,
    ];

    return $this->collection . '/?' . http_build_query($queryParams);
  }

  public function getObjectFromResponse($link = null, $location = null, $value = null, $rawValue = null)
  {
    return new SearchResult($this->collection, $value, $rawValue);
  }
}
