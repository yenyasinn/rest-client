<?php

namespace RestClient\Interceptor;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;
use RestClient\RequestExecutionInterface;

class CallableInterceptor implements RequestInterceptorInterface
{
    /**
     * @var callable    Signature: callable(RequestInterface $request, ContextInterface $context): RequestInterface
     */
    private $callable;

    /**
     * Signature: callable(RequestInterface $request, ContextInterface $context): RequestInterface
     *
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        $f = $this->callable;
        $request = $f($request, $context);
        return $execution->execute($request, $context);
    }
}
