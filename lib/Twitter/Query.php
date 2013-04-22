<?php

namespace Twitter;

use Buzz\Message\RequestInterface;
use Twitter\OAuth2\Consumer;

class Query implements QueryInterface
{
    /**
     * @var string
     */
    private $api_method;

    /**
     * @var string GET|POST|PUT|DELETE
     */
    private $http_method = 'GET';

    /**
     * @var array $headers - HTTP headers
     */
    private $headers = array();

    /**
     * @var string $content to be send to API endpoint
     */
    private $content = '';

    /**
     * @var array $query_string to be send to API
     */
    private $query_string = array();

    public function getUrl()
    {
        if($this->getHttpMethod() == RequestInterface::METHOD_GET)
            return sprintf('%s/%s?%s', Consumer::API_ENDPOINT, $this->api_method, http_build_query($this->getQueryString()));
        else
            return sprintf('%s/%s', Consumer::API_ENDPOINT, $this->api_method);
    }

    public function getHttpMethod()
    {
        return $this->http_method;
    }

    public function setHttpMethod($http_method)
    {
        $this->http_method = $http_method;

        return $this;
    }

    public function getApiMethod()
    {
        return $this->api_method;
    }

    public function setApiMethod($api_method)
    {
        $this->api_method = $api_method;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getQueryString()
    {
        return $this->query_string;
    }

    public function setQueryString($query_string)
    {
        if(is_array($query_string))
            $this->query_string = $query_string;
        else
        {
            $string = preg_match('/^\?/',$query_string) ? substr($query_string, 1) : $query_string;
            parse_str($string, $this->query_string);
        }

        return $this;
    }
}