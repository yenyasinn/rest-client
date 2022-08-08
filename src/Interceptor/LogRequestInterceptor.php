<?php declare(strict_types=1);

namespace RestClient\Interceptor;

use RestClient\RequestExecutionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class LogRequestInterceptor implements RequestInterceptorInterface
{
    private LoggerInterface $logger;
    private ?string $level;

    public function __construct(LoggerInterface $logger, string $level = LogLevel::INFO)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    public function intercept(RequestInterface $request, RequestExecutionInterface $execution): ResponseInterface
    {
        $response = $execution->execute($request);

        $this->logger->log($this->level, \sprintf('[%s] URI: %s', $request->getMethod(), $request->getUri()), [
            'request_headers' => $request->getHeaders(),
            'request_body' => $execution->getRequestBody(),
            'response_headers' => $response->getHeaders(),
            'response_body' => $execution->getResponseBody(),
        ]);

        return $response;
    }
}
