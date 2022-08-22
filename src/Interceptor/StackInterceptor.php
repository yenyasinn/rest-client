<?php declare(strict_types=1);

namespace RestClient\Interceptor;

final class StackInterceptor implements StackInterface
{
    private int $offset;
    private array $stack;
    private RequestInterceptorInterface $coreInterceptor;

    /**
     * @param RequestInterceptorInterface $coreInterceptor
     * @param array<RequestInterceptorInterface> $interceptors
     */
    public function __construct(RequestInterceptorInterface $coreInterceptor, array $interceptors = [])
    {
        $this->offset = 0;
        $this->coreInterceptor = $coreInterceptor;
        $this->stack = $interceptors;
    }

    public function next(): RequestInterceptorInterface
    {
        if (null === $next = ($this->stack[$this->offset] ?? null)) {
            return $this->coreInterceptor;
        }
        ++$this->offset;
        return $next;
    }
}
