<?php declare(strict_types=1);

namespace RestClient;

use RestClient\Exception\RestClientResponseException;
use RestClient\Serialization\ResponseBodyDecoderInterface;
use RestClient\Serialization\JsonResponseBodyDecoder;
use Psr\Http\Message\ResponseInterface;

class DefaultResponseErrorHandler implements ResponseErrorHandlerInterface
{
    /** @var array<ResponseBodyDecoderInterface> */
    private array $decoders;
    /** @var callable|null */
    private $bodyConvertFunction;

    public function __construct(?callable $bodyConvertFunction = null, array $decoders = [])
    {
        $this->decoders = $decoders;
        if (empty($this->decoders)) {
            $this->setDecoders([new JsonResponseBodyDecoder()]);
        }
        $this->setBodyConvertFunction($bodyConvertFunction);
    }

    public function setBodyConvertFunction(?callable $bodyConvertFunction): void
    {
        $this->bodyConvertFunction = $bodyConvertFunction;
    }

    public function setDecoders(array $decoders): void
    {
        $this->decoders = $decoders;
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
        $status = $response->getStatusCode();
        $phrase = $response->getReasonPhrase();
        $headers = $response->getHeaders();
        $body = $response->getBody()->getContents();
        $bodyDecoder = null;

        if (!empty($body)) {
            $bodyDecoder = $this->getBodyDecoder($response);
        }

        throw new RestClientResponseException(
            $phrase,
            $status,
            $phrase,
            $body,
            $headers,
            $bodyDecoder,
            $this->bodyConvertFunction
        );
    }

    private function getBodyDecoder(ResponseInterface $response): ?ResponseBodyDecoderInterface
    {
        foreach ($this->decoders as $decoder) {
            if ($decoder->canDecode($response)) {
                return $decoder;
            }
        }
        return null;
    }
}
