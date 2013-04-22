<?php

namespace Twitter;

class DefaultConverter implements ConverterInterface
{
    public function convert($input)
    {
        return json_decode($input, true);
    }
}