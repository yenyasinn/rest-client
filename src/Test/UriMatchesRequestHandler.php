<?php

namespace RestClient\Test;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ImmutableResponse;

class UriMatchesRequestHandler implements RequestHandlerInterface
{
    private string $uriPattern;

    /**
     * @var callable Example: fn(RequestInterface $request) => ImmutableResponse::create('{"message": "ok"}')
     *                        fn(RequestInterface $request) => '{"message": "ok"}'
     */
    private $handler;

    public function __construct(string $uriPattern, callable $handler)
    {
        $this->uriPattern = $uriPattern;
        $this->handler = $handler;
    }

    /**
     * @param string $pattern
     * @param string|callable $handler
     * @throws \RuntimeException
     * @return RequestHandlerInterface
     */
    public static function create(string $pattern, $handler): RequestHandlerInterface
    {
        if (\is_callable($handler)) {
            return new UriMatchesRequestHandler($pattern, $handler);
        }
        // string
        return new UriMatchesRequestHandler($pattern, fn() => $handler);
    }

    public function canHandle(RequestInterface $request): bool
    {
        return \preg_match($this->uriPattern, (string)$request->getUri());
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
