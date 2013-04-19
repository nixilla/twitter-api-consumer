<?php

namespace Twitter\Test;

use Twitter\DefaultConverter;

class DefaultConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        $input = array('first', 'second' => 'third', 'fourth' => array());

        $converter = new DefaultConverter();

        $this->assertEquals($input, $converter->convert(json_encode($input)), 'DefaultConvert does not return correct result');
    }
}