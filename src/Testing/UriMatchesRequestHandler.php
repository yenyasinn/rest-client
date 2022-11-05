<?php

namespace RestClient\Testing;

use Psr\Http\Message\RequestInterface;

class UriMatchesRequestHandler extends RequestHandler
{
    private string $uriPattern;

    public function __construct(string $method, string $uriPattern, callable $handler)
    {
        parent::__construct($handler, $method);
        $this->uriPattern = $uriPattern;
    }

    /**
     * @param string $method
     * @param string $pattern
     * @param string|callable $handler
     * @throws \RuntimeException
     * @return RequestHandlerInterface
     */
    public static function create(string $method, string $pattern, $handler): RequestHandlerInterface
    {
        if (\is_callable($handler)) {
            return new UriMatchesRequestHandler($method, $pattern, $handler);
        }
        // string
        return new UriMatchesRequestHandler($method, $pattern, fn() => $handler);
    }

    public function canHandle(RequestInterface $request): bool
    {
        return parent::canHandle($request) && \preg_match($this->uriPattern, \strtolower((string)$request->getUri()));
    }
}
