<?php

namespace RestClient\Tests\Interceptor;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;
use RestClient\Interceptor\RequestInterceptorInterface;
use RestClient\RequestExecutionInterface;

class AddCtxValueInterceptor implements RequestInterceptorInterface
{
    private string $key;
    /** @var mixed */
    private $value;

    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        $context->set($this->key, $this->value);
        return $execution->execute($request, $context);
    }
}
