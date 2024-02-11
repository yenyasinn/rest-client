<?php

namespace RestClient\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\Context;
use RestClient\ContextInterface;
use RestClient\Interceptor\AddHeaderInterceptor;
use RestClient\Interceptor\RequestIdInterceptor;
use RestClient\Interceptor\StackInterceptor;
use RestClient\RequestExecution;
use RestClient\Tests\Interceptor\TestInterceptor;

class RequestIdInterceptorTest extends TestCase
{
    public function testDefaultIdGenerator(): void
    {
        $requestContext = new Context();
        $request = Helper::createRequest('GET', '/test');

        $stack = new StackInterceptor(new TestInterceptor(function (RequestInterface $request, ContextInterface $context, ResponseInterface $response) {
            if ($request->hasHeader('Request-ID')) {
                $context->set('request_id', $request->getHeaderLine('Request-ID'));
                return $response->withHeader('Request-ID', $request->getHeaderLine('Request-ID'));
            }
            return $response;
        }), [
           new RequestIdInterceptor(),
        ]);

        $response = $stack->next()->intercept(
            $request,
            $requestContext,
            new RequestExecution($stack)
        );

        $this->assertArrayHasKey('Request-ID', $response->getHeaders());
        $this->assertEquals($requestContext->get('request_id'), $response->getHeaderLine('Request-ID'));
    }

    public function testAddHeader(): void
    {
        $requestContext = new Context();
        $request = Helper::createRequest('GET', '/test');

        $stack = new StackInterceptor(new TestInterceptor(function (RequestInterface $request, ContextInterface $context, ResponseInterface $response) {
            if ($request->hasHeader('_test_')) {
                $context->set('_test_', $request->getHeaderLine('_test_'));
            }
            return $response;
        }), [
            new AddHeaderInterceptor('_test_', '_1_'),
        ]);

        $stack->next()->intercept(
            $request,
            $requestContext,
            new RequestExecution($stack)
        );

        $this->assertEquals('_1_', $requestContext->get('_test_'));
    }
}
