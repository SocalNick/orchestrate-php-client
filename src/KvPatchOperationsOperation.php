<?php

namespace SocalNick\Orchestrate;

class KvPatchOperationsOperation extends KvPutOperation implements PatchOperationInterface
{
  public function __construct($collection, $key, $conditional = [])
  {
    parent::__construct($collection, $key, [], $conditional);
  }

  public function getData()
  {
    return json_encode($this->data);
  }

  protected function pathValueOperation($operation, $path, $value)
  {
    $this->data[] = [
      'op' => $operation,
      'path' => $path,
      'value' => $value,
    ];
    return $this;
  }

  protected function fromPathOperation($operation, $from, $path)
  {
    $this->data[] = [
      'op' => $operation,
      'from' => $from,
      'path' => $path,
    ];
    return $this;
  }

  public function add($path, $value)
  {
    return $this->pathValueOperation(__FUNCTION__, $path, $value);
  }

  public function replace($path, $value)
  {
    return $this->pathValueOperation(__FUNCTION__, $path, $value);
  }

  public function test($path, $value)
  {
    return $this->pathValueOperation(__FUNCTION__, $path, $value);
  }

  public function inc($path, $value)
  {
    return $this->pathValueOperation(__FUNCTION__, $path, $value);
  }

  public function move($from, $path)
  {
    return $this->fromPathOperation(__FUNCTION__, $from, $path);
  }

  public function copy($from, $path)
  {
    return $this->fromPathOperation(__FUNCTION__, $from, $path);
  }

  public function remove($path)
  {
    $this->data[] = [
      'op' => __FUNCTION__,
      'path' => $path,
    ];
    return $this;
  }
}
