<?php

namespace RestClient\Testing;

use Psr\Http\Message\RequestInterface;

class UriEqualsRequestHandler extends RequestHandler
{
    private string $expectedUri;

    public function __construct(string $method, string $expectedUri, callable $handler)
    {
        parent::__construct($handler, $method);
        $this->expectedUri = $expectedUri;
    }

    /**
     * @param string$method
     * @param string $uri
     * @param string|callable $handler
     * @throws \RuntimeException
     * @return RequestHandlerInterface
     */
    public static function create(string $method, string $uri, $handler): RequestHandlerInterface
    {
        if (\is_callable($handler)) {
            return new UriEqualsRequestHandler($method, $uri, $handler);
        }
        // string
        return new UriEqualsRequestHandler($method, $uri, fn() => $handler);
    }

    public function canHandle(RequestInterface $request): bool
    {
        return parent::canHandle($request) && $this->expectedUri === \strtolower((string)$request->getUri());
    }
}
