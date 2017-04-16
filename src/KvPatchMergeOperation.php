<?php

namespace SocalNick\Orchestrate;

class KvPatchMergeOperation extends KvPutOperation implements PatchOperationInterface
{
  public function getHeaders()
  {
    $headers = parent::getHeaders();
    $headers['Content-Type'] = 'application/merge-patch+json';

    return $headers;
  }

  public function getData()
  {
    return json_encode(parent::getData());
  }
}
