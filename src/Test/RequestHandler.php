<?php

namespace RestClient\Test;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ImmutableResponse;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var callable Example: fn(RequestInterface $request) => ImmutableResponse::create('{"message": "ok"}')
     *                        fn(RequestInterface $request) => '{"message": "ok"}'
     */
    private $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function canHandle(RequestInterface $request): bool
    {
        return true;
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
