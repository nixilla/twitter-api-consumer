<?php

namespace Twitter;

class TwitterSearchConverter implements ConverterInterface
{
    public function convert($input)
    {
        $result = json_decode($input, true);

        return array('data' => $result['statuses'], 'metainfo' => $result['search_metadata']);
    }
}
