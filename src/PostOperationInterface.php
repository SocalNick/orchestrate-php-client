<?php

namespace SocalNick\Orchestrate;

interface PostOperationInterface extends OperationInterface
{
  public function getData();
  public function getHeaders();
}
