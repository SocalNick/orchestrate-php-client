<?php

namespace SocalNick\Orchestrate;

class KvDeleteOperation extends KvFetchOperation implements DeleteOperationInterface
{

  public function getObjectFromResponse($ref, $value = null, $rawValue = null)
  {
    return true;
  }
}
