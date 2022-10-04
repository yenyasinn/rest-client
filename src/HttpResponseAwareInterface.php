<?php

namespace RestClient;

use Psr\Http\Message\ResponseInterface;

interface HttpResponseAwareInterface
{
    public function getHttpResponse(): ResponseInterface;
    public function setHttpResponse(ResponseInterface $response): void;
}
