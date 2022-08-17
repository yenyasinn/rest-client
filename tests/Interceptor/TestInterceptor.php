<?php

namespace RestClient\Tests\Interceptor;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;
use RestClient\Interceptor\RequestInterceptorInterface;
use RestClient\RequestExecutionInterface;

class TestInterceptor implements RequestInterceptorInterface
{
    /**
     * @var callable|null   Signature: callable(RequestInterface $request, ContextInterface $context, ResponseInterface $response): ResponseInterface
     */
    private $callable;

    public function __construct(?callable $callable = null)
    {
        $this->callable = $callable;
    }

    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        $response = new Response(200);
        if (null !== $this->callable) {
            $f = $this->callable;
            $response = $f($request, $context, $response);
        }
        return $response;
    }
}
