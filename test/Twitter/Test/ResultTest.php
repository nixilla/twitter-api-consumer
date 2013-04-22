<?php

namespace Twitter\Test;

use Twitter\Query;
use Twitter\QueryInterface;
use Twitter\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testAccessorsMutators()
    {
        $obj = new Result(array());

        $this->assertEquals(0, count($obj), 'Object should be empty');
        $this->assertNull($obj->getQuery());
        $this->assertNull($obj->nextQuery());

        $this->assertEquals(array(), $obj->toArray(), 'not empty array');

        $this->assertEquals($obj, $obj->setQuery(new Query()), 'Fluent interface problem');

        $this->assertTrue($obj->getQuery() instanceof QueryInterface, 'Not QueryInterface');
        $this->assertTrue($obj->nextQuery() instanceof QueryInterface, 'Not QueryInterface');

        $obj = new Result(array('data' => array('1'), 'metainfo' => array('next_results' => 'since_id=12321')));

        $this->assertEquals(array('1'), $obj->toArray(), 'arrays not match');

        $obj->setQuery(new Query());

        $this->assertEquals(1, count($obj), 'Object should not be empty');

        $this->assertEquals(array('since_id' => '12321'), $obj->nextQuery()->getQueryString(), 'Query string does not match');
    }

    public function testArrayAccess()
    {
        $obj = new Result(array('data' => array('key' => 'val'), 'metainfo' => array('next_results' => 'since_id=12321')));

        $this->assertNotNull($obj['key'], 'Key is not set');

        $this->assertTrue(isset($obj['key']), 'Key is not set');

        unset($obj['key']);

        $this->assertNull($obj['key'], 'Key is still set');

        $obj['key'] = '1';

        $this->assertNotNull($obj['key'], 'Key is not set');

        $obj[] = 2;

        $this->assertEquals(2, count($obj), 'Object count should be 2');
    }
}