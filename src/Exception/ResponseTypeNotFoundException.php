<?php declare(strict_types=1);

namespace RestClient\Exception;

class ResponseTypeNotFoundException extends RestClientException
{
    public function __construct(string $expectedResponseType)
    {
        parent::__construct(\sprintf('Response type not found: "%s"', $expectedResponseType));
    }
}
