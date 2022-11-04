<?php

namespace RestClient\Test;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TestClient implements ClientInterface
{
    /** @var array<RequestHandlerInterface>  */
    private array $handlers;
    private RequestHandlerInterface $defaultHandler;

    public function __construct(RequestHandlerInterface $defaultHandler, array $handlers = [])
    {
        $this->setDefaultHandler($defaultHandler);
        $this->setHandlers($handlers);
    }

    public function setHandlers(array $handlers): void
    {
        $this->handlers = [];
        foreach ($handlers as $expr => $handler) {
            if (\is_string($expr) && (\is_callable($handler) || \is_string($handler))) {
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

    public function getDefaultHandler(): RequestHandlerInterface
    {
        return $this->defaultHandler;
    }

    public function setDefaultHandler(RequestHandlerInterface $defaultHandler): void
    {
        $this->defaultHandler = $defaultHandler;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return ($this->findHandler($request) ?? $this->defaultHandler)->handle($request);
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

    /**
     * @param string $expr
     * @param string|callable $handler
     * @return RequestHandlerInterface
     */
    private function createHandler(string $expr, $handler): RequestHandlerInterface
    {
        $expr = \strtolower($expr);

        if (\strpos($expr, 'equal:') === 0) {
            $handlerName = 'equal';
        } elseif(\strpos($expr, 'match:') === 0) {
            $handlerName = 'match';
        } else {
            $handlerName = 'equal';
        }

        if ('match' === $handlerName) {
            return UriMatchesRequestHandler::create(\ltrim($expr,'match:'), $handler);
        }

        return UriEqualsRequestHandler::create(\ltrim($expr,'equal:'), $handler);
    }
}
