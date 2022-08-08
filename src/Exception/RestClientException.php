<?php declare(strict_types=1);

namespace RestClient\Exception;

use Psr\Http\Client\ClientExceptionInterface;

class RestClientException extends \RuntimeException
{
    public static function fromClientException(ClientExceptionInterface $clientException): RestClientException
    {
        return new self($clientException->getMessage(), $clientException->getCode(), $clientException);
    }
}
