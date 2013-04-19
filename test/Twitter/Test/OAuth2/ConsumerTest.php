<?php

namespace Twitter\Test\OAuth2;

use Buzz\Message\RequestInterface;
use Twitter\DefaultConverter;
use Twitter\OAuth2\Consumer;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Twitter\Exception\TwitterApiException
     */
    public function testCallException1()
    {
        $client = $this->getClient();

        $response = $this->getResponse(200, array('token_type' => 'bearer','access_token' => 'abc1234'));

        $client
            ->expects($this->at(0))
            ->method('call')
            ->with(sprintf('%s/oauth2/token', Consumer::API_ENDPOINT), 'POST')
            ->will($this->returnValue($response));

        $response = $this->getResponse(403, array('errors' => array(array('message' => 'Fake error message', 'code' => '1'))));

        $client
            ->expects($this->at(1))
            ->method('call')
            ->with(
                sprintf('%s/1.1/search/tweets.json?%s', Consumer::API_ENDPOINT, http_build_query(array('q' => urlencode('@nixilla')))),
                RequestInterface::METHOD_GET,
                array(
                    'User-Agent' => 'Twitter API Consumer (https://github.com/nixilla/twitter-api-consumer)',
                    'Authorization' =>  sprintf('Bearer %s', 'abc1234')
                ),
                ''
            )
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'aaaa', 'bbbb');

        $consumer->call('/1.1/search/tweets.json', RequestInterface::METHOD_GET, array('q' => urlencode('@nixilla')));
    }

    public function testCall()
    {
        $client = $this->getClient();

        $response = $this->getResponse(200, array('token_type' => 'bearer','access_token' => 'abc1234'));

        $client
            ->expects($this->at(0))
            ->method('call')
            ->with(sprintf('%s/oauth2/token', Consumer::API_ENDPOINT), 'POST')
            ->will($this->returnValue($response));

        $response = $this->getResponse(200, array('some value'));

        $client
            ->expects($this->at(1))
            ->method('call')
            ->with(
                sprintf('%s/1.1/search/tweets.json?%s', Consumer::API_ENDPOINT, http_build_query(array('q' => urlencode('@nixilla')))),
                RequestInterface::METHOD_GET,
                array(
                    'User-Agent' => 'Twitter API Consumer (https://github.com/nixilla/twitter-api-consumer)',
                    'Authorization' =>  sprintf('Bearer %s', 'abc1234')
                ),
                ''
            )
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'aaaa', 'bbbb');

        $consumer->call('/1.1/search/tweets.json', RequestInterface::METHOD_GET, array('q' => urlencode('@nixilla')));
    }

    /**
     * @expectedException Twitter\Exception\TwitterApiException
     */
    public function testObtainBearerTokenException1()
    {
        $client = $this->getClient();

        $response = $this->getResponse(403, array('errors' => array(array('message' => 'Fake error message', 'code' => '1'))));

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/oauth2/token', Consumer::API_ENDPOINT), 'POST')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'aaaa', 'bbbb');
        $consumer->obtainBearerToken();
    }

    /**
     * @expectedException \Exception
     */
    public function testObtainBearerTokenException2()
    {
        $client = $this->getClient();

        $response = $this->getResponse(403);

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/oauth2/token', Consumer::API_ENDPOINT), 'POST')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'aaaa', 'bbbb');
        $consumer->obtainBearerToken();
    }

    /**
     * @expectedException Twitter\Exception\InvalidTokenTypeException
     */
    public function testObtainBearerTokenException3()
    {
        $client = $this->getClient();

        $response = $this->getResponse(200, array('token_type' => 'random','access_token' => 'abc1234'));

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/oauth2/token', Consumer::API_ENDPOINT), 'POST')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'aaaa', 'bbbb');
        $consumer->obtainBearerToken();
    }

    public function testObtainBearerToken()
    {
        $client = $this->getClient();

        $response = $this->getResponse(200, array('token_type' => 'bearer','access_token' => 'abc1234'));

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/oauth2/token', Consumer::API_ENDPOINT), 'POST')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'aaaa', 'bbbb');
        $this->assertEquals('abc1234', $consumer->obtainBearerToken(), 'Token do not match');
    }

    public function testConstructor()
    {
        $client = $this->getClient();
        $consumer = new Consumer($client, 'aaaa', 'bbbb');
        $this->assertAttributeEquals($client, 'client', $consumer);
        $this->assertAttributeEquals('aaaa', 'consumer_key', $consumer);
        $this->assertAttributeEquals('bbbb', 'consumer_secret', $consumer);
    }

    public function testSetConverter()
    {
        $client = $this->getClient();
        $consumer = new Consumer($client, 'consumer_key', 'consumer_secret');
        $converter = $this->getMock('Twitter\Converter', array('convert'));
        $consumer->setConverter('/me', $converter);
    }

    public function testGetConverter()
    {
        $client = $this->getClient();
        $consumer = new Consumer($client, 'consumer_key', 'consumer_secret');

        $this->assertTrue($consumer->getConverter('/me') instanceof DefaultConverter);

        $converter = $this->getMock('Twitter\Converter', array('convert'));
        $consumer->setConverter('/me', $converter);

        $this->assertEquals($converter, $consumer->getConverter('/me'));

        $consumer->setConverter('/some/strange/api/method/with/strange/characters/łąóąłąłąóżżźźżąóąłąó', $converter);
        $this->assertEquals($converter, $consumer->getConverter('/some/strange/api/method/with/strange/characters/łąóąłąłąóżżźźżąóąłąó'));
    }

    private function getClient()
    {
        return $this->getMock('Buzz\Browser', array('call', 'get'));
    }

    private function getResponse($code, $content = null)
    {
        $response = $this->getMock('Buzz\Message\Response', array('isSuccessful', 'isClientError', 'isServerError', 'getContent'));

        $response
            ->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue($code == 200));

        $response
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(json_encode($content)));

        return $response;
    }
}