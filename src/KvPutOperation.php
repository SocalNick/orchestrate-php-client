<?php

namespace SocalNick\Orchestrate;

class KvPutOperation extends KvFetchOperation implements PutOperationInterface
{
  protected $data;
  protected $conditional;

  public function __construct($collection, $key, $data, $conditional = [])
  {
    parent::__construct($collection, $key);
    $this->data = $data;
    $this->conditional = $conditional;
  }

  public function getHeaders()
  {
    $headers = [
      'Content-Type' => 'application/json',
    ];

    if (array_key_exists('if-match', $this->conditional)) {
      $headers['If-Match'] = "\"{$this->conditional['if-match']}\"";
    } elseif (array_key_exists('if-none-match', $this->conditional)) {
      $headers['If-None-Match'] = '"*"';
    }

    return $headers;
  }

  public function getData()
  {
    return $this->data;
  }

  public function getObjectFromResponse($ref, $location = null, $value = null, $rawValue = null)
  {
    return new KvObject($this->collection, $this->key, $ref);
  }
}
