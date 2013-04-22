<?php

namespace Twitter;

class Result implements ResultInterface, \ArrayAccess, \Countable
{
    /**
     * @var \Twitter\QueryInterface
     */
    private $query;

    /**
     * @var array for ArrayAccess
     */
    private $container = array();

    /**
     * @var array for result pagination
     */
    private $metainfo = array();

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if(isset($data['data']) && isset($data['metainfo']))
        {
            $this->container = $data['data'];
            $this->metainfo = $data['metainfo'];
        }
        else $this->container = $data;
    }

    /**
     * @return \Twitter\QueryInterface
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param \Twitter\QueryInterface $query
     * @return \Twitter\ResultInterface
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return QueryInterface
     */
    public function nextQuery()
    {
        if(isset($this->metainfo['next_results']))
            return $this->query->setQueryString($this->metainfo['next_results']);
        else
            return $this->query;
    }

    /**
     * ArrayAccess methods
     */

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) $this->container[] = $value;
        else $this->container[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->container);
    }
}