<?php

namespace RestClient\Testing;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ImmutableResponse;

class RequestHandler implements RequestHandlerInterface
{
    private string $method;
    /**
     * @var callable Example: fn(RequestInterface $request) => ImmutableResponse::create('{"message": "ok"}')
     *                        fn(RequestInterface $request) => '{"message": "ok"}'
     *                        fn(RequestInterface $request) => ['status' => 200, 'body' => '{"message": "ok"}', 'headers' => []]
     *                        fn(RequestInterface $request) => ['status' => 200, 'json' => ['message' => 'ok'], 'headers' => []]
     */
    private $handler;

    public function __construct(callable $handler, string $method = 'any')
    {
        $this->method = \strtolower($method);
        $this->handler = $handler;
    }

    public function canHandle(RequestInterface $request): bool
    {
        if (empty($this->method) || 'any' === $this->method || '*' === $this->method) {
            return true;
        }
        return $this->method === \strtolower($request->getMethod());
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $h = $this->handler;
        $ret = $h($request);

        if ($ret instanceof ResponseInterface) {
            return $ret;
        }

        if (\is_string($ret)) {
            return ImmutableResponse::create($ret);
        }

        if (\is_array($ret)) {
            if (empty($ret)) {
                return ImmutableResponse::create();
            }
            $status = $ret['status'] ?? 200;
            $body = $ret['body'] ?? '';
            $json = $ret['json'] ?? [];
            if (!empty($json)) {
                $body = \json_encode($json, JSON_THROW_ON_ERROR);
            }
            $headers = $ret['headers'] ?? [];
            return ImmutableResponse::create($body, $status, $headers);
        }

        throw new \RuntimeException('Unknown handler result');
    }
}
