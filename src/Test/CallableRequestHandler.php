<?php

namespace RestClient\Test;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ImmutableResponse;

class CallableRequestHandler implements RequestHandlerInterface
{
    /**
     * @var callable Example: fn(RequestInterface $request) => true|false
     */
    private $predicate;

    /**
     * @var callable Example: fn(RequestInterface $request) => ImmutableResponse::create('{"message": "ok"}')
     *                        fn(RequestInterface $request) => '{"message": "ok"}'
     */
    private $handler;

    public function __construct(callable $predicate, callable $handler)
    {
        $this->predicate = $predicate;
        $this->handler = $handler;
    }

    public function canHandle(RequestInterface $request): bool
    {
        $p = $this->predicate;
        return $p($request);
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
