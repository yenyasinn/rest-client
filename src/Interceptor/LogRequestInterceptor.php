<?php declare(strict_types=1);

namespace RestClient\Interceptor;

use RestClient\ContextInterface;
use RestClient\Exception\RestClientResponseException;
use RestClient\ImmutableResponse;
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
    protected LoggerInterface $logger;
    protected string $level;
    protected int $bodyTruncSize;

    public function __construct(LoggerInterface $logger, string $level = LogLevel::INFO, int $bodyTruncSize = 0)
    {
        $this->logger = $logger;
        $this->level = $level;
        $this->bodyTruncSize = $bodyTruncSize;
    }

    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        $this->logBeforeRequest($request, $context);

        try {
            $response = $execution->execute($request, $context);
            $this->logAfterRequest($request, $context, $response);
            return $response;
        } catch (RestClientResponseException $exception) {
            $this->logException($request, $context, $exception);
            throw $exception;
        }
    }

    /**
     * Default format: "[beforeRequest] [<HTTP_METHOD>] URI: <REQUEST_URI>"
     *
     * @param RequestInterface $request
     * @param ContextInterface $context
     * @return void
     */
    protected function logBeforeRequest(RequestInterface $request, ContextInterface $context): void
    {
        $message = \sprintf('[beforeRequest] [%s] URI: %s', $request->getMethod(), $request->getUri());
        $this->logger->log($this->level, $message, [
            'request_headers' => $request->getHeaders(),
        ]);
    }

    /**
     * Default format: "[afterRequest] [<HTTP_METHOD>] URI: <REQUEST_URI> [<RESPONSE_STATUS_CODE>]"
     *
     * @param RequestInterface $request
     * @param ContextInterface $context
     * @param ResponseInterface $response
     * @return void
     */
    protected function logAfterRequest(RequestInterface $request, ContextInterface $context, ResponseInterface $response): void
    {
        $message = \sprintf('[afterRequest] [%s] URI: %s [%s]',
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode());
        $this->logger->log($this->level, $message, [
            'request_headers' => $request->getHeaders(),
            'request_body' => ctx_request_get_body($context),
            'response_headers' => $response->getHeaders(),
            'response_body' => $this->extractResponseBody($context),
        ]);
    }

    /**
     * Default format: "[exceptionRequest] [<HTTP_METHOD>] URI: <REQUEST_URI> [<RESPONSE_STATUS_CODE>] <EXCEPTION_MESSAGE>"
     *
     * @param RequestInterface $request
     * @param ContextInterface $context
     * @param RestClientResponseException $exception
     * @return void
     */
    protected function logException(RequestInterface $request, ContextInterface $context, RestClientResponseException $exception): void
    {
        $exceptionClass = \get_class($exception);
        $response = ImmutableResponse::fromRestClientResponseException($exception);
        $message = \sprintf('[exceptionRequest] [%s] URI: %s [%s] %s',
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            $exception->getMessage());
        $this->logger->error($message, [
            'exception_class' => $exceptionClass,
            'request_headers' => $request->getHeaders(),
            'response_headers' => $exception->getHeaders(),
        ]);
    }

    private function extractResponseBody(ContextInterface $context): ?string
    {
        if (!ctx_response_has_body($context)) {
            return null;
        }
        return ctx_response_get_body($context, $this->bodyTruncSize);
    }
}
