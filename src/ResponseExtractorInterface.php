<?php

namespace RestClient;

use Psr\Http\Message\ResponseInterface;
use RestClient\Exception\UnknownContentTypeException;

interface ResponseExtractorInterface
{
    /**
     * Returns NULL when HTTP response does not have body.
     *
     * @param ResponseInterface $response
     * @param string $targetType
     * @return ResponseData
     * @throws UnknownContentTypeException
     */
    public function extractData(ResponseInterface $response, string $targetType = 'array'): ResponseData;
}
