<?php

namespace RestClient\Tests\Interceptor;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;
use RestClient\Interceptor\RequestInterceptorInterface;
use RestClient\RequestExecutionInterface;

class OkCoreInterceptor implements RequestInterceptorInterface
{
    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        return new Response(200);
    }
}
