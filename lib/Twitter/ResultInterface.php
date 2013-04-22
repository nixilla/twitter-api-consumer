<?php

namespace Twitter;

interface ResultInterface
{
    /**
     * @return \Twitter\QueryInterface
     */
    public function getQuery();

    /**
     * @param \Twitter\QueryInterface $query
     * @return \Twitter\ResultInterface
     */
    public function setQuery($query);

    /**
     * @return QueryInterface
     */
    public function nextQuery();
}