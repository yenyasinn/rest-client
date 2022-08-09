<?php

namespace RestClient\Interceptor;

use RestClient\ContextInterface;
use RestClient\RequestExecutionInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestInterceptorInterface
{
    /**
     * @throws ClientExceptionInterface
     */
    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface;
}
