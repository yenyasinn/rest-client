<?php

namespace RestClient\Retry;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;

interface RetryStrategyInterface
{
    public function shouldRetry(RequestInterface $request, ContextInterface $context, ResponseInterface $response): bool;

    /**
     * Must return interval in milliseconds.
     * REST client uses this value to wait before retrying request.
     *
     * @param RequestInterface $request
     * @param ContextInterface $context
     * @param ResponseInterface $response
     * @return int
     */
    public function getRetryInterval(RequestInterface $request, ContextInterface $context, ResponseInterface $response): int;
}
