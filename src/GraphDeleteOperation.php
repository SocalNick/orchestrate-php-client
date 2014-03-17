<?php

namespace SocalNick\Orchestrate;

class GraphDeleteOperation extends GraphPutOperation implements DeleteOperationInterface
{

  public function getEndpoint()
  {
    return parent::getEndpoint() . '?purge=true';
  }

  public function getObjectFromResponse($ref, $value = null, $rawValue = null)
  {
    return true;
  }
}
