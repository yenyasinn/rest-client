<?php declare(strict_types=1);

namespace RestClient\Interceptor;

final class StackInterceptor implements StackInterface
{
    private \SplStack $stack; // TODO: replace by array
    private RequestInterceptorInterface $coreInterceptor;

    /**
     * @param RequestInterceptorInterface $coreInterceptor
     * @param array<RequestInterceptorInterface> $interceptors
     */
    public function __construct(RequestInterceptorInterface $coreInterceptor, array $interceptors = [])
    {
        $this->coreInterceptor = $coreInterceptor;
        $this->stack = new \SplStack();
        foreach ($interceptors as $interceptor) {
            $this->stack->push($interceptor);
        }
        $this->stack->rewind();
    }

    public function next(): RequestInterceptorInterface
    {
        if (null === $next = $this->stack->current()) {
            return $this->coreInterceptor;
        }
        $this->stack->next();
        return $next;
    }
}
