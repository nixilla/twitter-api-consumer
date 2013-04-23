<?php

namespace Twitter\Test;

use Twitter\TwitterSearchConverter;

class TwitterSearchConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        $input = array(
            'statuses' => array('first', 'second' => 'third', 'fourth' => array()),
            'search_metadata' => array('next_results' => '?max_id=123')
        );

        $converter = new TwitterSearchConverter();

        $output = $converter->convert(json_encode($input));

        $this->assertEquals($input['statuses'], $output['data'], 'TwitterSearchConvert does not return correct result');
        $this->assertEquals($input['search_metadata'], $output['metainfo'], 'TwitterSearchConvert does not return correct result');
    }
}