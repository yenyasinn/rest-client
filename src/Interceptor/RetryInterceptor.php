<?php

namespace RestClient\Interceptor;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\Exception\RestClientResponseException;
use RestClient\ImmutableResponse;
use RestClient\Retry\DefaultRetryStrategy;
use RestClient\Retry\RetryStrategyInterface;
use RestClient\ContextInterface;
use RestClient\RequestExecutionInterface;

class RetryInterceptor implements RequestInterceptorInterface
{
    private RetryStrategyInterface $retryStrategy;

    public function __construct(?RetryStrategyInterface $retryStrategy = null)
    {
        $this->retryStrategy = $retryStrategy ?? new DefaultRetryStrategy(2, 500);
    }

    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        $tempExecution = clone $execution;

        $response = $this->safeExecute($request, $context, $execution);

        for (;;) {
            if (false === $this->retryStrategy->shouldRetry($request, $context, $response)) {
                break;
            }

            $intervalMs = $this->retryStrategy->getRetryInterval($request, $context, $response);

            if ($intervalMs > 0) {
                \usleep($this->toMicroseconds($intervalMs));
            }

            $response = $this->safeExecute($request, $context, $tempExecution);
        }

        return $response;
    }

    private function safeExecute(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        $isolatedExecution = clone $execution;
        try {
            return $isolatedExecution->execute($request, $context);
        } catch (RestClientResponseException $exception) {
            return ImmutableResponse::fromRestClientResponseException($exception);
        }
    }

    private function toMicroseconds(int $ms): int
    {
        return 1000 * $ms;
    }
}
