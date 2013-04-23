<?php

namespace Twitter\Test;

use Twitter\Query;
use Twitter\QueryInterface;
use Twitter\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testAccessorsMutators()
    {
        $obj = new Result(array(), new Query());

        $this->assertEquals(0, count($obj), 'Object should be empty');
        $this->assertNotNull($obj->getQuery());
        $this->assertFalse($obj->nextQuery());

        $this->assertEquals(array(), $obj->getMetainfo());

        $this->assertEquals(array(), $obj->toArray(), 'not empty array');

        $this->assertEquals($obj, $obj->setQuery(new Query()), 'Fluent interface problem');

        $this->assertTrue($obj->getQuery() instanceof QueryInterface, 'Not QueryInterface');

        $obj = new Result(array('data' => array('1'), 'metainfo' => array('next_results' => 'max_id=12321')), new Query());

        $this->assertEquals(array('next_results' => 'max_id=12321'), $obj->getMetainfo());

        $this->assertEquals(array('1'), $obj->toArray(), 'arrays not match');

        $this->assertEquals(1, count($obj), 'Object should not be empty');

        $this->assertEquals(array('max_id' => '12321'), $obj->nextQuery()->getQueryString(), 'Query string does not match');
    }

    public function testArrayAccess()
    {
        $obj = new Result(array('data' => array('key' => 'val'), 'metainfo' => array('next_results' => 'since_id=12321')), new Query());

        $this->assertNotNull($obj['key'], 'Key is not set');

        $this->assertTrue(isset($obj['key']), 'Key is not set');

        unset($obj['key']);

        $this->assertNull($obj['key'], 'Key is still set');

        $obj['key'] = '1';

        $this->assertNotNull($obj['key'], 'Key is not set');

        $obj[] = 2;

        $this->assertEquals(2, count($obj), 'Object count should be 2');
    }

    public function testIteratorInterface()
    {
        $data =  array(
            'key5' => 'val5',
            'key7' => 'val7'
        );

        $obj = new Result(array('data' => $data, 'metainfo' => array()), new Query());

        foreach($obj as $key => $val)
        {
            $this->assertEquals($data[$key], $val, 'value does not match');
        }
    }
}