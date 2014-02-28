<?php

namespace SocalNick\Orchestrate;

interface PutOperationInterface extends OperationInterface
{
  public function getData();
  public function getHeaders();
}
