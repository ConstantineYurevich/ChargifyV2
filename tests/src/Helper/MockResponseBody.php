<?php

namespace ChargifyV2\Test\Helper;

class MockResponseBody
{
    public static function read($filename)
    {
        return file_get_contents(dirname(dirname(__DIR__)) . '/data/mock/' . $filename);
    }
}