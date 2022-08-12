<?php

namespace RestClient;

use Psr\Http\Message\ResponseInterface;
use RestClient\Converter\ConverterInterface;
use RestClient\Converter\JsonConverter;
use RestClient\Exception\RestClientException;
use RestClient\Exception\UnknownContentTypeException;
use function RestClient\Helpers\response_has_message_body;

class ResponseConverterExtractor implements ResponseExtractorInterface
{
    /** @var array<ConverterInterface> */
    private array $converters;

    /**
     * @param array<ConverterInterface> $converters
     */
    public function __construct(array $converters = [])
    {
        $this->setConverters($converters);
    }

    /**
     * @param array<ConverterInterface> $converters
     * @return void
     */
    public function setConverters(array $converters): void
    {
        $this->converters = $converters;
        if (empty($this->converters)) {
            $this->converters[] = new JsonConverter();
        }
    }

    public function extractData(ResponseInterface $response, string $targetType = 'array'): ResponseData
    {
        if (!response_has_message_body($response)) {
            return new ResponseData(null, '');
        }

        if (!$response->getBody()->isReadable()) {
            throw new RestClientException('Response body stream is not readable');
        }

        $contentType = $response->getHeaderLine('Content-Type');

        foreach ($this->converters as $converter) {
            if ($converter->canConvert($targetType, $contentType)) {
                $responseBody = $response->getBody()->getContents();
                $responseData = $converter->convert($targetType, $contentType, $responseBody);
                return new ResponseData($responseData, $responseBody);
            }
        }

        throw new UnknownContentTypeException(
            $targetType,
            $contentType,
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getHeaders(),
            $response->getBody()->getContents()
        );
    }
}
