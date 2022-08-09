<?php

namespace RestClient\Helpers;

use RestClient\ContextInterface;

function asList(string $className): string
{
    return $className . '[]';
}

function ctxRequestHasPayload(ContextInterface $context): bool
{
    return $context->has(ContextInterface::REQUEST_PAYLOAD);
}

function ctxRequestGetPayload(ContextInterface $context): ?object
{
    return $context->get(ContextInterface::REQUEST_PAYLOAD);
}

function ctxResponseAsList(ContextInterface $context): bool
{
    return $context->get('response_as_list', false);
}

function ctxResponseGetType(ContextInterface $context): string
{
    return $context->get(ContextInterface::RESPONSE_TYPE, '');
}

function ctxRequestGetBody(ContextInterface $context): ?string
{
    return $context->get(ContextInterface::REQUEST_BODY);
}

function ctxResponseHasBody(ContextInterface $context): bool
{
    return $context->has(ContextInterface::RESPONSE_BODY);
}

function ctxResponseGetBody(ContextInterface $context, int $truncSize = 0): ?string
{
    $body = $context->get(ContextInterface::RESPONSE_BODY);
    if (null === $body || $truncSize <= 0) {
        return $body;
    }
    return \substr($body, 0, $truncSize);
}
