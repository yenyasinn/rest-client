<?php declare(strict_types=1);

namespace RestClient\Exception;

class RestClientResponseException extends RestClientException
{
    private int $statusCode;
    private string $phrase;
    private string $responseBody;
    private array $headers;
    /** @var mixed */
    private $data;

    /**
     * @param string $message
     * @param int $statusCode
     * @param string $phrase
     * @param string $responseBody
     * @param array $headers
     * @param mixed $data
     */
    public function __construct(string $message, int $statusCode, string $phrase, string $responseBody, array $headers, $data)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->phrase = $phrase;
        $this->responseBody = $responseBody;
        $this->headers = $headers;
        $this->data = $data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
