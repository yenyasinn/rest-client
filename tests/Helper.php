<?php

namespace RestClient\Tests;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class Helper
{
    public static function createRequest(string $method, string $uri): RequestInterface
    {
        return new Request($method, $uri);
    }
}
