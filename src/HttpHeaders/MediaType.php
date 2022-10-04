<?php

namespace RestClient\HttpHeaders;

final class MediaType
{
    public const TEXT = 'text';
    public const TEXT_HTML = 'text/html';
    public const TEXT_PLAIN = 'text/plain';
    public const APPLICATION_JSON = 'application/json';
    public const APPLICATION_XML = 'application/xml';

    private function __construct()
    {
    }
}
