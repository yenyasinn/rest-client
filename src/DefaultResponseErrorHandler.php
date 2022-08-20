<?php declare(strict_types=1);

namespace RestClient;

use RestClient\Exception\HttpClientErrorException;
use RestClient\Exception\HttpServerErrorException;
use RestClient\Exception\RestClientResponseException;
use Psr\Http\Message\ResponseInterface;
use RestClient\Exception\UnknownHttpStatusCodeException;

class DefaultResponseErrorHandler implements ResponseErrorHandlerInterface
{
    private ?string $targetType;
    private ?ResponseExtractorInterface $responseExtractor;

    public function __construct()
    {
        $this->setTargetType(null);
        $this->setResponseExtractor(null);
    }

    /**
     * @param string|null $targetType A full qualified class name or 'array' to convert to array.
     * @return void
     */
    public function setTargetType(?string $targetType): void
    {
        $this->targetType = $targetType;
    }

    public function setResponseExtractor(?ResponseExtractorInterface $responseExtractor): void
    {
        $this->responseExtractor = $responseExtractor;
    }

    public function hasError(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return ($statusCode >= 400 && $statusCode <= 499) || ($statusCode >= 500 && $statusCode <= 599);
    }

    /**
     * @param ResponseInterface $response
     * @return void
     * @throws RestClientResponseException
     */
    public function handleError(ResponseInterface $response): void
    {
        if (empty($this->targetType) || null === $this->responseExtractor) {
            $responseData = new ResponseData(null, $response->getBody()->getContents());
        } else {
            $responseData = $this->responseExtractor->extractData($response, $this->targetType);
        }
        $statusCode = $response->getStatusCode();
        $phrase = $response->getReasonPhrase();
        $headers = $response->getHeaders();
        $message = $this->getErrorMessage($statusCode, $phrase, $responseData->getResponseBody());

        if ($statusCode >= 400 && $statusCode <= 499) {
            throw new HttpClientErrorException(
                $message,
                $statusCode,
                $phrase,
                $responseData->getResponseBody(),
                $headers,
                $responseData->getResponseData(),
                $response->getProtocolVersion(),
            );
        }

        if ($statusCode >= 500 && $statusCode <= 599) {
            throw new HttpServerErrorException(
                $message,
                $statusCode,
                $phrase,
                $responseData->getResponseBody(),
                $headers,
                $responseData->getResponseData(),
                $response->getProtocolVersion(),
            );
        }

        throw new UnknownHttpStatusCodeException(
            $this->getErrorMessage($statusCode, $phrase, $responseData->getResponseBody()),
            $statusCode,
            $phrase,
            $responseData->getResponseBody(),
            $headers,
            $responseData->getResponseData()
        );
    }

    private function getErrorMessage(int $statusCode, string $phrase, string $body): string
    {
        $preface = $statusCode . ' ' . $phrase . ': ';
        $content = empty($body) ? '[no body]' : '[' . $body . ']';
        return $preface . $content;
    }
}
