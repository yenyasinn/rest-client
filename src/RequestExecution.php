<?php declare(strict_types=1);

namespace RestClient;

use RestClient\Interceptor\StackInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RequestExecution implements RequestExecutionInterface
{
    private StackInterface $stack;

    public function __construct(StackInterface $stack)
    {
        $this->stack = $stack;
    }

    public function execute(RequestInterface $request, ContextInterface $context): ResponseInterface
    {
        return $this->stack->next()->intercept($request, $context, $this);
    }

    public function __clone()
    {
        $this->stack = clone $this->stack;
    }
}
