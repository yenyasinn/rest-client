<?php declare(strict_types=1);

namespace RestClient\Exception;

use RestClient\Serialization\ResponseBodyDecoderInterface;

class RestClientResponseException extends RestClientException
{
    private int $statusCode;
    private string $responsePhrase;
    private string $responseBody;
    private array $responseHeaders;
    private ?ResponseBodyDecoderInterface $bodyDecoder;
    private ?array $decodedBody;
    /** @var callable|null  */
    private $converterFunction;
    private ?object $convertedBody;

    public function __construct(string $message, int $statusCode, string $responsePhrase, string $responseBody, array $responseHeaders, ?ResponseBodyDecoderInterface $bodyDecoder, ?callable $converterFunction)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->responsePhrase = $responsePhrase;
        $this->responseBody = $responseBody;
        $this->responseHeaders = $responseHeaders;
        $this->bodyDecoder = $bodyDecoder;
        $this->converterFunction = $converterFunction;
        $this->decodedBody = null;
        $this->convertedBody = null;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponsePhrase(): string
    {
        return $this->responsePhrase;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    public function getDecodedBody(): ?array
    {
        if ((null === $this->decodedBody) && null !== $this->bodyDecoder) {
            $this->decodedBody = $this->bodyDecoder->decode($this->responseBody);
        }
        return $this->decodedBody;
    }

    public function getConvertedBody(): ?object
    {
        if (null === $this->convertedBody && null !== $this->converterFunction && null !== $this->getDecodedBody()) {
            $f = $this->converterFunction;
            $this->convertedBody = $f($this->getDecodedBody(), $this);
        }
        return $this->convertedBody;
    }
}
