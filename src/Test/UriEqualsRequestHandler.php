<?php

namespace RestClient\Test;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ImmutableResponse;

class UriEqualsRequestHandler implements RequestHandlerInterface
{
    private string $expectedUri;
    /**
     * @var callable Example: fn(RequestInterface $request) => ImmutableResponse::create('{"message": "ok"}')
     *                        fn(RequestInterface $request) => '{"message": "ok"}'
     */
    private $handler;

    public function __construct(string $expectedUri, callable $handler)
    {
        $this->expectedUri = $expectedUri;
        $this->handler = $handler;
    }

    /**
     * @param string $uri
     * @param string|callable $handler
     * @throws \RuntimeException
     * @return RequestHandlerInterface
     */
    public static function create(string $uri, $handler): RequestHandlerInterface
    {
        if (\is_callable($handler)) {
            return new UriEqualsRequestHandler($uri, $handler);
        }
        // string
        return new UriEqualsRequestHandler($uri, fn() => $handler);
    }

    public function canHandle(RequestInterface $request): bool
    {
        return $this->expectedUri === (string)$request->getUri();
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $h = $this->handler;
        $ret = $h($request);
        if (\is_string($ret)) {
            $ret = ImmutableResponse::create($ret);
        }
        return $ret;
    }
}
