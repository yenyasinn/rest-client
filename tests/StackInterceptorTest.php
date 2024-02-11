<?php

namespace RestClient\Tests;

use PHPUnit\Framework\TestCase;
use RestClient\Context;
use RestClient\Interceptor\StackInterceptor;
use RestClient\RequestExecution;
use RestClient\Tests\Interceptor\AddCtxValueInterceptor;
use RestClient\Tests\Interceptor\BeforeAfterInterceptor;
use RestClient\Tests\Interceptor\TestInterceptor;

class StackInterceptorTest extends TestCase
{
    public function testNext(): void
    {
        $requestContext = new Context();

        $stack = new StackInterceptor(new TestInterceptor(), [
            new BeforeAfterInterceptor(),
            new AddCtxValueInterceptor('first', 1),
            new AddCtxValueInterceptor('second', 2)
        ]);

        $stack->next()->intercept(
            Helper::createRequest('GET', '/test'),
            $requestContext,
            new RequestExecution($stack)
        );

        $this->assertEquals(['Before request', 'first', 'second', 'After request'], $requestContext->getKeys());
    }
}
