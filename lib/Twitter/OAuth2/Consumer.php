<?php

namespace Twitter\OAuth2;

use Buzz\Message\RequestInterface;
use Buzz\Message\Response;
use Twitter\ConsumerInterface;
use Twitter\ConverterInterface;
use Twitter\DefaultConverter;
use Twitter\Exception\InvalidTokenTypeException;
use Twitter\Exception\TwitterApiException;
use Twitter\Query;
use Twitter\QueryInterface;
use Twitter\Result;
use Twitter\ResultInterface;

class Consumer implements ConsumerInterface
{
    const API_ENDPOINT = 'https://api.twitter.com';

    private $consumer_key, $consumer_secret, $access_token;
    private $converters = array();

    public function __construct($client, $consumer_key, $consumer_secret)
    {
        $this->client = $client;
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
    }

    /**
     * Lets us know if something went wrong
     *
     * @param Response $response
     * @throws \Twitter\Exception\TwitterApiException
     * @throws \Exception
     */
    protected function handleException(Response $response)
    {
        $json_string = $response->getContent();
        $output = json_decode($json_string, true);

        if(count($output['errors']))
            throw new TwitterApiException($output['errors'][0]['message'],$output['errors'][0]['code']);
        else
            throw new \Exception(sprintf('Unknown error: %s', $json_string));
    }

    /**
     * Gets OAuth2 access_token from Twitter API
     *
     * @throws \Twitter\Exception\InvalidTokenTypeException
     * @return string
     */
    public function obtainBearerToken()
    {
        $credentials = $this->getCredentials();

        $headers = array(
            'Authorization' => sprintf('Basic %s', $credentials),
            'User-Agent' => 'Twitter API Consumer (https://github.com/nixilla/twitter-api-consumer)'
        );

        $response = $this->client->post(sprintf('%s/oauth2/token', static::API_ENDPOINT), $headers, 'grant_type=client_credentials');

        if($response->isSuccessful())
        {
            $token = json_decode($response->getContent(), true);

            if($token['token_type'] == 'bearer')
            {
                $this->access_token = $token['access_token'];
                return $this->access_token;
            }
            else
                throw new InvalidTokenTypeException(sprintf('Token type should be "bearer", but "%s" given', $token['token_type']));

        }
        else
            return $this->handleException($response);
    }

    /**
     * Gets converted obj for given API method
     *
     * @param $api_method
     * @return Twitter\ConverterInterface
     */
    public function getConverter($api_method)
    {
        $api_method = $this->normalizeApiMethod($api_method);

        if(isset($this->converters[$api_method]))
            return $this->converters[$api_method];
        else
            return new DefaultConverter();
    }

    /**
     * Sets converter object for given API method
     *
     * @param $api_method
     * @param ConverterInterface $converter
     */
    public function setConverter($api_method, ConverterInterface $converter)
    {
        $api_method = $this->normalizeApiMethod($api_method);

        $this->converters[$api_method] = $converter;
    }

    /**
     * Creates OAuth2 credentials code
     *
     * @return string
     */
    private function getCredentials()
    {
        return base64_encode(sprintf('%s:%s', $this->consumer_key, $this->consumer_secret));
    }

    /**
     * Adds OAuth2 headers, but they can be overriden
     *
     * @param $headers
     * @return array
     */
    private function getHeaders($headers)
    {
        return array_merge(
            array(
                'User-Agent' => 'Twitter API Consumer (https://github.com/nixilla/twitter-api-consumer)',
                'Authorization' =>  sprintf('Bearer %s', $this->access_token)
            ),
            $headers
        );
    }

    /**
     * @param $api_method
     * @param string $http_method
     * @param array $query_string
     * @param array $headers
     * @param string $content
     * @return QueryInterface
     */
    public function prepare($api_method, $http_method = 'GET', array $query_string = array(), $headers = array(), $content = '')
    {
        if( ! $this->access_token) $this->obtainBearerToken();

        $query = new Query();

        $query->setApiMethod($this->normalizeApiMethod($api_method));
        $query->setHttpMethod($http_method);
        $query->setHeaders($this->getHeaders($headers));
        $query->setContent($content);
        $query->setQueryString($query_string);

        return $query;
    }

    /**
     * @param \Twitter\QueryInterface|null $query
     * @return \Twitter\ResultInterface
     */
    public function execute($query)
    {
        if( ! $query instanceof QueryInterface) return false;

        $response = $this->client->call(
            $query->getUrl(),
            $query->getHttpMethod(),
            $query->getHeaders(),
            $query->getContent()
        );

        if($response->isSuccessful())
        {
            $converter = $this->getConverter($query->getApiMethod());
            $result = $converter->convert($response->getContent());
            return new Result($result, $query);
        }
        else return $this->handleException($response);
    }

    /**
     * removes slash (/) form the beginning of the API method signature
     * /api/method -> api/method
     *
     * @param $api_method
     * @return string
     */
    private function normalizeApiMethod($api_method)
    {
        return preg_match('/^\//',$api_method) ? substr($api_method, 1) : $api_method;
    }
}