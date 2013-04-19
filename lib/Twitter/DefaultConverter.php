<?php

namespace Twitter;

class DefaultConverter implements Converter
{
    public function convert($input)
    {
        return json_decode($input, true);
    }
}