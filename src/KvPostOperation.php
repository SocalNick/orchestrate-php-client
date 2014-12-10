<?php

namespace SocalNick\Orchestrate;

class KvPostOperation extends KvFetchOperation implements PostOperationInterface
{
  protected $data;

  public function __construct($collection, $data)
  {
    $this->collection = $collection;
    $this->data = $data;
  }

  public function getHeaders()
  {
    $headers = array(
      'Content-Type' => 'application/json',
    );

    return $headers;
  }

  public function getData()
  {
    return $this->data;
  }

  public function getEndpoint()
  {
    return $this->collection;
  }
 
  public function getObjectFromResponse($ref, $value = null, $rawValue = null)
  {
    return new KvObject($this->collection, $value['path']['key'], $ref, $value);
  }
}
