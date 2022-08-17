<?php declare(strict_types=1);

namespace RestClient\Interceptor;

use RestClient\ContextInterface;
use RestClient\RequestExecutionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use function RestClient\Helpers\ctx_request_get_body;
use function RestClient\Helpers\ctx_response_get_body;
use function RestClient\Helpers\ctx_response_has_body;

class LogRequestInterceptor implements RequestInterceptorInterface
{
    private LoggerInterface $logger;
    private string $level;
    private int $bodyTruncSize;

    public function __construct(LoggerInterface $logger, string $level = LogLevel::INFO, int $bodyTruncSize = 0)
    {
        $this->logger = $logger;
        $this->level = $level;
        $this->bodyTruncSize = $bodyTruncSize;
    }

    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        // [Before request].
        // For example at this place we can alter request headers.

        $response = $execution->execute($request, $context);

        // [After request]
        // For example at this place we can log response.

        $this->logger->log(
            $this->level,
            $this->createLogMessage($request, $context),
            $this->createLogContext($request, $response, $context),
        );

        return $response;
    }

    protected function createLogMessage(RequestInterface $request, ContextInterface $context): string
    {
        return \sprintf('[%s] URI: %s', $request->getMethod(), $request->getUri());
    }

    protected function createLogContext(RequestInterface $request, ResponseInterface $response, ContextInterface $context): array
    {
        return[
            'request_headers' => $request->getHeaders(),
            'request_body' => ctx_request_get_body($context),
            'response_headers' => $response->getHeaders(),
            'response_body' => $this->extractResponseBody($context),
        ];
    }

    private function extractResponseBody(ContextInterface $context): ?string
    {
        if (!ctx_response_has_body($context)) {
            return null;
        }
        return ctx_response_get_body($context, $this->bodyTruncSize);
    }
}
