<?php

namespace RestClient\Interceptor;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;
use RestClient\RequestExecutionInterface;

class AddHeaderInterceptor implements RequestInterceptorInterface
{
    private string $headerName;
    private string $headerValue;

    public function __construct(string $headerName, string $headerValue)
    {
        $this->headerName = $headerName;
        $this->headerValue = $headerValue;
    }

    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        return $execution->execute($request->withHeader($this->headerName, $this->headerValue), $context);
    }
}
