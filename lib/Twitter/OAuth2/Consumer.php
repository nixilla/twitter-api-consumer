<?php

namespace Twitter\OAuth2;

use Buzz\Message\RequestInterface;
use Buzz\Message\Response;
use Twitter\Converter;
use Twitter\DefaultConverter;
use Twitter\Exception\InvalidTokenTypeException;
use Twitter\Exception\TwitterApiException;

class Consumer
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

    public function call($api_method, $http_method = RequestInterface::METHOD_GET, array $query_string = array(), $headers = array(), $content = '')
    {
        if( ! $this->access_token) $this->obtainBearerToken();

        if(preg_match('/^\//',$api_method)) $api_method = substr($api_method, 1);

        $url = sprintf('%s/%s?%s', self::API_ENDPOINT, $api_method, http_build_query($query_string));

        $response = $this->client->call($url, $http_method, $this->getHeaders($headers), $content);

        if($response->isSuccessful())
        {
            $converter = $this->getConverter($api_method);
            return $converter->convert($response->getContent());
        }
        else return $this->handleException($response);
    }

    protected function handleException(Response $response)
    {
        $json_string = $response->getContent();
        $output = json_decode($json_string, true);

        if(count($output['errors']))
            throw new TwitterApiException($output['errors'][0]['message'],$output['errors'][0]['code']);
        else
            throw new \Exception(sprintf('Unknown error: %s', $json_string));
    }

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

    public function getConverter($api_method)
    {
        if(preg_match('/^\//',$api_method)) $api_method = substr($api_method, 1);

        if(isset($this->converters[$api_method]))
            return $this->converters[$api_method];
        else
            return new DefaultConverter();
    }

    public function setConverter($api_method, $converter)
    {
        if( ! $converter instanceof Converter)
            throw new \InvalidArgumentException('Second argument must implement Converter interface');

        if(preg_match('/^\//',$api_method)) $api_method = substr($api_method, 1);

        $this->converters[$api_method] = $converter;
    }

    private function getCredentials()
    {
        return base64_encode(sprintf('%s:%s', $this->consumer_key, $this->consumer_secret));
    }

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
}