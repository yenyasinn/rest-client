<?php

namespace RestClient\Helpers;

use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;

function asList(string $className): string
{
    return $className . '[]';
}

function response_has_message_body(ResponseInterface $response): bool
{
    $noBodyStatuses = [100, 101, 102, 103, 204, 304];
    if (\in_array($response->getStatusCode(), $noBodyStatuses, true)) {
        return false;
    }
    return 0 !== $response->getBody()->getSize();
}

function ctx_request_has_model(ContextInterface $context): bool
{
    return $context->has(ContextInterface::REQUEST_MODEL);
}

function ctx_request_get_model(ContextInterface $context): ?object
{
    return $context->get(ContextInterface::REQUEST_MODEL);
}

function ctx_response_as_list(ContextInterface $context): bool
{
    return $context->get('response_as_list', false);
}

function ctx_response_get_type(ContextInterface $context): string
{
    return $context->get(ContextInterface::RESPONSE_TYPE, '');
}

function ctx_request_get_body(ContextInterface $context): ?string
{
    return $context->get(ContextInterface::REQUEST_BODY);
}

function ctx_response_has_body(ContextInterface $context): bool
{
    return $context->has(ContextInterface::RESPONSE_BODY);
}

function ctx_response_get_body(ContextInterface $context, int $truncSize = 0): ?string
{
    $body = $context->get(ContextInterface::RESPONSE_BODY);
    if (null === $body || $truncSize <= 0) {
        return $body;
    }
    return \substr($body, 0, $truncSize);
}
