<?php

namespace RestClient\Tests;

use PHPStan\Testing\TestCase;
use RestClient\Configuration\DefaultConfiguration;
use RestClient\DefaultJsonRestClient;

class RestClientTest extends TestCase
{
    public function testCreate(): void
    {
        $rest = new DefaultJsonRestClient(DefaultConfiguration::create('http://localhost:8000'));

        $this->assertEquals('application/json', $rest->getHttpHeaders()->getContentType());
    }
}
