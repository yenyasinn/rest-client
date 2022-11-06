<?php

namespace RestClient\Testing;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TestClient implements ClientInterface
{
    /** @var array<RequestHandlerInterface>  */
    private array $handlers;
    private ?RequestHandlerInterface $defaultHandler;

    public function __construct(array $handlers = [], ?RequestHandlerInterface $defaultHandler = null)
    {
        $this->setDefaultHandler($defaultHandler);
        $this->setHandlers($handlers);
    }

    public function setHandlers(array $handlers): void
    {
        $this->handlers = [];
        foreach ($handlers as $expr => $handler) {
            if (\is_string($expr) && (\is_callable($handler) || \is_string($handler) || \is_array($handler))) {
                $this->handlers[] = $this->createHandler($expr, $handler);
            } elseif($handler instanceof RequestHandlerInterface) {
                $this->handlers[] = $handler;
            } else {
                throw new \RuntimeException('Unknown handler definition');
            }
        }
    }

    public function getHandlers(): array
    {
        return $this->handlers;
    }

    public function getDefaultHandler(): ?RequestHandlerInterface
    {
        return $this->defaultHandler;
    }

    public function setDefaultHandler(?RequestHandlerInterface $defaultHandler): void
    {
        $this->defaultHandler = $defaultHandler;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return ($this->findHandler($request) ?? $this->defaultHandler ?? $this->createEmptyHandler())->handle($request);
    }

    private function findHandler(RequestInterface $request): ?RequestHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($request)) {
                return $handler;
            }
        }
        return null;
    }

    private function createEmptyHandler(): RequestHandlerInterface
    {
        return new RequestHandler(fn() => '');
    }

    /**
     * @param string $expr
     * @param string|callable $handler
     * @return RequestHandlerInterface
     */
    private function createHandler(string $expr, $handler): RequestHandlerInterface
    {
        $expr = \strtolower($expr);

        [$method, $params] = $this->parseExpr($expr);

        $uri = $params['uri'] ?? '/';
        $matcher = $params['matcher'] ?? 'eq';

        if ('re' === $matcher) {
            return UriMatchesRequestHandler::create($method, $uri, $handler);
        }

        return UriEqualsRequestHandler::create($method, $uri, $handler);
    }

    private function parseExpr(string $expr): array
    {
        if (false === \strpos($expr, '?')) {
            $expr = 'get?' . $expr;
        }

        $methodDefPos = \strpos($expr, '?');
        $method = \substr($expr, 0, $methodDefPos);
        $params = [];
        $paramsDef = \explode('&', \substr($expr, $methodDefPos + 1));
        foreach ($paramsDef as $def) {
            $param = \explode('=', $def);
            $params[$param[0]] = $param[1];
        }

        return [
            $method,
            $params,
        ];
    }
}
