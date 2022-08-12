<?php

namespace RestClient\Exception;

class UnknownContentTypeException extends RestClientException
{
    private string $targetType;
    private string $contentType;
    private int $statusCode;
    private string $phrase;
    private array $headers;
    private ?string $body;

    public function __construct(string $targetType, string $contentType, int $statusCode, string $phrase, array $headers, ?string $body)
    {
        parent::__construct(\sprintf('Could not extract response: no suitable Converter found for target type [%s] and content type [%s]', $targetType, $contentType));
        $this->targetType = $targetType;
        $this->contentType = $contentType;
        $this->statusCode = $statusCode;
        $this->phrase = $phrase;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getTargetType(): string
    {
        return $this->targetType;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }
}
