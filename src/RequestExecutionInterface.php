<?php

namespace RestClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestExecutionInterface
{
    public function execute(RequestInterface $request, ContextInterface $context): ResponseInterface;
}
