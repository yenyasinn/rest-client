<?php declare(strict_types=1);

namespace RestClient\Interceptor;

use RestClient\ContextInterface;
use RestClient\RequestExecutionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use function RestClient\Helpers\ctxRequestGetBody;
use function RestClient\Helpers\ctxResponseGetBody;
use function RestClient\Helpers\ctxResponseHasBody;

final class LogRequestInterceptor implements RequestInterceptorInterface
{
    private LoggerInterface $logger;
    private ?string $level;
    private int $bodyTruncSize;

    public function __construct(LoggerInterface $logger, string $level = LogLevel::INFO, int $bodyTruncSize = 0)
    {
        $this->logger = $logger;
        $this->level = $level;
        $this->bodyTruncSize = $bodyTruncSize;
    }

    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        $response = $execution->execute($request, $context);

        $this->logger->log($this->level, \sprintf('[%s] URI: %s', $request->getMethod(), $request->getUri()), [
            'request_headers' => $request->getHeaders(),
            'request_body' => ctxRequestGetBody($context),
            'response_headers' => $response->getHeaders(),
            'response_body' => $this->extractResponseBody($context),
        ]);

        return $response;
    }

    private function extractResponseBody(ContextInterface $context): ?string
    {
        if (!ctxResponseHasBody($context)) {
            return null;
        }
        return ctxResponseGetBody($context, $this->bodyTruncSize);
    }
}
