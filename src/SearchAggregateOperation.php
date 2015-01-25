<?php

namespace SocalNick\Orchestrate;

class SearchAggregateOperation implements OperationInterface
{
  protected $collection;
  protected $aggregate;
  protected $query = '*';

  public function __construct($collection, $aggregate, $query = '*')
  {
    $this->collection = $collection;
    $this->aggregate = $aggregate;
    $this->query = $query;
  }

  public function getEndpoint()
  {
    $queryParams = [
      'aggregate' => $this->aggregate,
      'query' => $this->query,
    ];

    return $this->collection . '/?' . http_build_query($queryParams);
  }

  public function getObjectFromResponse($link = null, $location = null, $value = null, $rawValue = null)
  {
    return new SearchResult($this->collection, $value, $rawValue);
  }
}
