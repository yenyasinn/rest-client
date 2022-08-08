<?php

namespace RestClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestExecutionInterface
{
    public function execute(RequestInterface $request): ResponseInterface;
    public function getContext(): RequestContext;

    /**
     * Important Note: A stream can be read only once.
     * This method should be used instead of Psr\Http\Message\ResponseInterface::getBody()->getContents()
     * @return string|null
     */
    public function getResponseBody(): ?string;

    /**
     * Important Note: A stream can be read only once.
     * This method should be used instead of Psr\Http\Message\RequestInterface::getBody()->getContents()
     * @return string|null
     */
    public function getRequestBody(): ?string;
}
