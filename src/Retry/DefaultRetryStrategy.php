<?php

namespace RestClient\Retry;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;

class DefaultRetryStrategy implements RetryStrategyInterface
{
    private const DEFAULT_RETRY_INTERVAL = 0;

    private int $attempts;
    private int $maxAttempts;
    private array $retriableStatusCodes;
    private int $interval;

    /**
     * Response status codes:
     * [429] Too Many Requests
     * [503] Service Unavailable
     *
     * @param int $maxAttempts  A max number of attempts
     * @param int $interval A retry interval in milliseconds
     * @param array $retriableStatusCodes A list of response status codes that should be retried
     */
    public function __construct(int $maxAttempts = 1, int $interval = self::DEFAULT_RETRY_INTERVAL, array $retriableStatusCodes = [429, 503])
    {
        $this->attempts = 0;
        $this->maxAttempts = $maxAttempts;
        $this->retriableStatusCodes = $retriableStatusCodes;
        $this->interval = $interval;
    }

    public function shouldRetry(RequestInterface $request, ContextInterface $context, ResponseInterface $response): bool
    {
        if ($this->attempts >= $this->maxAttempts) {
            return false;
        }

        if (!\in_array($response->getStatusCode(), $this->retriableStatusCodes, true)) {
            return false;
        }

        if (!$this->isMethodIdempotent($request)) {
            return false;
        }

        $this->attempts++;

        return true;
    }

    public function getRetryInterval(RequestInterface $request, ContextInterface $context, ResponseInterface $response): int
    {
        /**
         * Retry-After: <http-date>
         * Retry-After: <delay-seconds>
         */
        if (!$response->hasHeader('Retry-After')) {
            return $this->interval;
        }

        $headerValue = $response->getHeaderLine('Retry-After');
        $intervalValue = 0;

        if (\is_numeric($headerValue)) {
            // Retry-After: <delay-seconds>
            $intervalValue = (int)$headerValue;
        } elseif(!empty($headerValue)) {
            // Retry-After: <http-date>
            $intervalValue = $this->getIntervalFromString($headerValue);
        }

        if ($intervalValue >= 0) {
            return $intervalValue * 1000; // to milliseconds
        }

        return $this->interval;
    }

    /**
     * @see https://www.mscharhag.com/api-design/http-idempotent-safe
     *
     * @param RequestInterface $request
     * @return bool
     */
    private function isMethodIdempotent(RequestInterface $request): bool
    {
        // NoIdempotent methods:
        // POST
        // PATCH
        return \in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS', 'TRACE', 'PUT', 'DELETE'], true);
    }

    private function getIntervalFromString(string $dateTime): int
    {
        try {
            return (new \DateTime($dateTime))->getTimestamp() - \time();
        } catch (\Exception $exception) {
            return -1;
        }
    }
}
