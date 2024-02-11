<?php

namespace RestClient;

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
        $this->body = new MockStream($body);
    }

    public static function create(string $body = '', int $statusCode = 200, array $headers = [], string $reasonPhrase = '', string $protocolVersion = '1.1'): ImmutableResponse
    {
        if (empty($reasonPhrase)) {
            $reasonPhrase = self::getDefaultReasonPhrase($statusCode);
        }
        return new self($protocolVersion, $statusCode, $reasonPhrase, $headers, $body);
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

    public function getHeader(string $name): array
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

    /**
     * @deprecated Please use 'getContents'.
     * @return string
     */
    public function getBodyAsString(): string
    {
        return $this->getContents();
    }

    public function getContents(): string
    {
        return $this->body->getContents();
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

    private static function getDefaultReasonPhrase(int $statusCode): string
    {
        switch ($statusCode) {
            case 100:	return 'Continue';
            case 101:	return 'Switching Protocols';
            case 200:	return 'OK';
            case 201:	return 'Created';
            case 202:	return 'Accepted';
            case 203:	return 'Non-Authoritative Information';
            case 204:	return 'No Content';
            case 205:	return 'Reset Content';
            case 206:	return 'Partial Content';
            case 300:	return 'Multiple Choices';
            case 301:	return 'Moved Permanently';
            case 302:	return 'Moved Temporarily';
            case 303:	return 'See Other';
            case 304:	return 'Not Modified';
            case 305:	return 'Use Proxy';
            case 400:	return 'Bad Request';
            case 401:	return 'Unauthorized';
            case 402:	return 'Payment Required';
            case 403:	return 'Forbidden';
            case 404:	return 'Not Found';
            case 405:	return 'Method Not Allowed';
            case 406:	return 'Not Acceptable';
            case 407:	return 'Proxy Authentication Required';
            case 408:	return 'Request Time-out';
            case 409:	return 'Conflict';
            case 410:	return 'Gone';
            case 411:	return 'Length Required';
            case 412:	return 'Precondition Failed';
            case 413:	return 'Request Entity Too Large';
            case 414:	return 'Request-URI Too Large';
            case 415:	return 'Unsupported Media Type';
            case 500:	return 'Internal Server Error';
            case 501:	return 'Not Implemented';
            case 502:	return 'Bad Gateway';
            case 503:	return 'Service Unavailable';
            case 504:	return 'Gateway Time-out';
            case 505:	return 'HTTP Version not supported';
        }
        return '';
    }
}
