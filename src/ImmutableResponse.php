<?php

namespace RestClient;

use GuzzleHttp\Psr7\BufferStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RestClient\Exception\RestClientResponseException;

final class ImmutableResponse implements ResponseInterface
{
    private string $protocolVersion;
    private array $headers;
    private array $loweredHeaders;
    private StreamInterface $body;
    private int $statusCode;
    private string $reasonPhrase;

    public function __construct(string $protocolVersion, int $statusCode, string $reasonPhrase, array $headers, string $body)
    {
        $this->protocolVersion = $protocolVersion;
        $this->headers = $headers;
        $this->loweredHeaders = $this->withLowerKeys($headers);
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
        $this->body = new BufferStream();
        $this->body->write($body);
    }

    public static function fromRestClientResponseException(RestClientResponseException $responseException): ResponseInterface
    {
        return new self(
            $responseException->getProtocolVersion(),
            $responseException->getStatusCode(),
            $responseException->getPhrase(),
            $responseException->getHeaders(),
            $responseException->getResponseBody()
        );
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version): ImmutableResponse
    {
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        return \array_key_exists(\strtolower($name), $this->loweredHeaders);
    }

    public function getHeader($name)
    {
        return $this->loweredHeaders[\strtolower($name)] ?? [];
    }

    public function getHeaderLine($name): string
    {
        $value = $this->loweredHeaders[\strtolower($name)] ?? [];
        return \implode(',', $value);
    }

    public function withHeader($name, $value): ImmutableResponse
    {
        return $this;
    }

    public function withAddedHeader($name, $value): ImmutableResponse
    {
        return $this;
    }

    public function withoutHeader($name): ImmutableResponse
    {
        return $this;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): ImmutableResponse
    {
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = ''): ImmutableResponse
    {
        return $this;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    private function withLowerKeys(array $headers): array
    {
        $lowered = [];
        foreach ($headers as $k => $v) {
            $lowered[\strtolower($k)] = $v;
        }
        return $lowered;
    }
}
