<?php

namespace RestClient\Interceptor;

use RestClient\RequestExecutionInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestInterceptorInterface
{
    /**
     * @throws ClientExceptionInterface
     */
    public function intercept(RequestInterface $request, RequestExecutionInterface $execution): ResponseInterface;
}
