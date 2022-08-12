<?php declare(strict_types=1);

namespace RestClient\Exception;

class UnknownTypeException extends RestClientException
{
    public function __construct(string $className)
    {
        parent::__construct(\sprintf('Class not found: "%s"', $className));
    }
}
