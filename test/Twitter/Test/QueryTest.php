<?php

namespace Twitter\Test;

use Twitter\OAuth2\Consumer;
use Twitter\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testAccessorsMutators()
    {
        $obj = new Query();

        $this->assertEquals(sprintf('%s/?', Consumer::API_ENDPOINT), $obj->getUrl(), 'Url do not match');

        $this->assertEquals('GET', $obj->getHttpMethod(), 'http_method should be GET');
        $this->assertEquals($obj, $obj->setHttpMethod('POST'), 'Fluent interface not working');
        $this->assertEquals('POST', $obj->getHttpMethod(), 'http_method should be POST');

        $this->assertEquals(sprintf('%s/', Consumer::API_ENDPOINT), $obj->getUrl(), 'Url do not match');

        $this->assertNull($obj->getApiMethod(), 'api_method should be null');
        $this->assertEquals($obj, $obj->setApiMethod('1.1/api/method'), 'Fluent interface not working');
        $this->assertEquals('1.1/api/method', $obj->getApiMethod(), 'api_method should be "1.1/api/method"');

        $this->assertEquals(array(), $obj->getHeaders(), 'headers should be empty array');
        $this->assertEquals($obj, $obj->setHeaders(array('X-Header','yes')), 'Fluent interface not working');
        $this->assertEquals(array('X-Header','yes'), $obj->getHeaders(), 'headers should be array - but not empty');

        $this->assertEquals('', $obj->getContent(), 'content should be empty string');
        $this->assertEquals($obj, $obj->setContent('test string'), 'Fluent interface not working');
        $this->assertEquals('test string', $obj->getContent(), 'content should be "test string"');

        $this->assertEquals(array(), $obj->getQueryString(), 'query_string should be empty array');
        $this->assertEquals($obj, $obj->setQueryString(array('key' => 'val')), 'Fluent interface not working');
        $this->assertEquals(array('key' => 'val'), $obj->getQueryString(), 'query_string should be non empty array');

        $obj->setQueryString('key1=val1&key2=val2');
        $this->assertEquals(array('key1' => 'val1', 'key2' => 'val2'), $obj->getQueryString(), 'query_string should be non empty array');
    }
}