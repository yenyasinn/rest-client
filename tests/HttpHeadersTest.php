<?php

namespace RestClient\Tests;

use PHPUnit\Framework\TestCase;
use RestClient\HttpHeaders\HttpHeaders;
use RestClient\HttpHeaders\MediaType;

class HttpHeadersTest extends TestCase
{
    public function testAccept(): void
    {
        $httpHeaders = new HttpHeaders();

        $httpHeaders->setAccept([MediaType::TEXT_HTML, MediaType::TEXT]);

        $this->assertEquals(['text/html', 'text'], $httpHeaders->getAccept());
    }

    public function testContentTypeEmpty(): void
    {
        $httpHeaders = new HttpHeaders();

        $this->assertEquals('', $httpHeaders->getContentType());
    }

    public function testContentType(): void
    {
        $httpHeaders = new HttpHeaders();

        $httpHeaders->setContentType(MediaType::APPLICATION_JSON);

        $this->assertEquals(MediaType::APPLICATION_JSON, $httpHeaders->getContentType());
    }

    public function testAccessControlAllowCredentials(): void
    {
        $httpHeaders = new HttpHeaders();

        $httpHeaders->setAccessControlAllowCredentials(true);

        $this->assertTrue($httpHeaders->getAccessControlAllowCredentials());

        $httpHeaders->setAccessControlAllowCredentials(false);

        $this->assertFalse($httpHeaders->getAccessControlAllowCredentials());
    }
}
