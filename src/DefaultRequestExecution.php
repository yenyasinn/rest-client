<?php declare(strict_types=1);

namespace RestClient;

use RestClient\Interceptor\StackInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class DefaultRequestExecution implements RequestExecutionInterface
{
    private StackInterface $stack;
    private RequestContext $requestContext;

    public function __construct(StackInterface $stack, ?RequestContext $requestContext)
    {
        $this->stack = $stack;
        $this->requestContext = $requestContext ?? new RequestContext();
    }

    public function execute(RequestInterface $request): ResponseInterface
    {
        return $this->stack->next()->intercept($request, $this);
    }

    public function getContext(): RequestContext
    {
        return $this->requestContext;
    }

    public function getResponseBody(): ?string
    {
        return $this->requestContext->get('response_body');
    }

    public function getRequestBody(): ?string
    {
        return $this->requestContext->get('request_body');
    }
}
