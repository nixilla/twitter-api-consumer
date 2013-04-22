<?php

namespace Twitter;

interface ConsumerInterface
{
    /**
     * @param $api_method
     * @param string $http_method
     * @param array $query_string
     * @param array $headers
     * @param string $content
     * @return mixed
     */
    public function prepare($api_method, $http_method = 'GET', array $query_string = array(), $headers = array(), $content = '');

    /**
     * @param QueryInterface $query
     * @return ResultInterface
     */
    public function execute(QueryInterface $query);
}