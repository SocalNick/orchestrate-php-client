<?php

namespace SocalNick\Orchestrate;

use Guzzle\Http\Message\Response;

interface OperationInterface
{
  public function getEndpoint();

  public function getObjectFromResponse($refLink, $location = null, $value = null, $rawValue = null);
}
