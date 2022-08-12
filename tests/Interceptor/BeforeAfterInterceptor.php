<?php

namespace RestClient\Tests\Interceptor;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;
use RestClient\Interceptor\RequestInterceptorInterface;
use RestClient\RequestExecutionInterface;

class BeforeAfterInterceptor implements RequestInterceptorInterface
{
    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        $context->set('Before request', 1);

        $response = $execution->execute($request, $context);

        $context->set('After request', 2);

        return $response;
    }
}
