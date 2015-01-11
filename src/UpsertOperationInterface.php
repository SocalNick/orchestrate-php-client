<?php

namespace SocalNick\Orchestrate;

interface UpsertOperationInterface extends OperationInterface
{
  public function getData();
  public function getHeaders();
}
