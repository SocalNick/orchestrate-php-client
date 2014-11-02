<?php

namespace SocalNick\Orchestrate;

class GraphFetchOperation implements OperationInterface
{
    protected $collection;
    protected $key;
    protected $kind;

    public function __construct($collection, $key, $kind, 
        $limit = 10, $offset = 0)
    {
        $this->collection = $collection;
        $this->key = $key;
        $this->kind = $kind;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getEndpoint()
    {
        $graphParams = array(
            'limit' => $this->limit,
            'offset' => $this->offset,
        );
        
        return $this->collection  . '/' . $this->key . 
            '/relations/' . $this->kind. '?' . http_build_query($graphParams);
    }

    public function getObjectFromResponse($ref, $value = null, $rawValue = null)
    {
        return new GraphObject($this->collection, $this->key, $this->kind, $value, $rawValue);
    }
}
