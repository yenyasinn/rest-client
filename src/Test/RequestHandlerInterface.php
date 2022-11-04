<?php

namespace RestClient\Test;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestHandlerInterface
{
    public function canHandle(RequestInterface $request): bool;
    public function handle(RequestInterface $request): ResponseInterface;
}
