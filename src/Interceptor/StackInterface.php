<?php

namespace RestClient\Interceptor;

interface StackInterface
{
    public function next(): RequestInterceptorInterface;
}
