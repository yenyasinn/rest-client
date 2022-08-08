<?php

namespace RestClient;

use RestClient\Exception\RestClientResponseException;
use Psr\Http\Message\ResponseInterface;

interface ResponseErrorHandlerInterface
{
    public function hasError(ResponseInterface $response): bool;

    /**
     * This method MUST throw an exception.
     *
     * @param ResponseInterface $response
     * @return void
     * @throws RestClientResponseException
     */
    public function handleError(ResponseInterface $response): void;
}
