<?php

namespace Twitter;

interface QueryInterface
{
    public function getUrl();

    public function getHttpMethod();

    public function setHttpMethod($http_method);

    public function getApiMethod();

    public function setApiMethod($api_method);

    public function getQueryString();

    public function setQueryString($query_string);

    public function getHeaders();

    public function setHeaders(array $headers);

    public function getContent();

    public function setContent($content);
}