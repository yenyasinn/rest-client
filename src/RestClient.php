<?php declare(strict_types=1);

namespace RestClient;

use RestClient\Exception\ResponseTypeNotFoundException;
use RestClient\Exception\RestClientException;
use RestClient\Interceptor\RequestInterceptorInterface;
use RestClient\Interceptor\StackInterceptor;
use RestClient\Serialization\SerializerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class RestClient implements RestClientInterface, RequestInterceptorInterface
{
    /** @var array<RequestInterceptorInterface> */
    private array $interceptors;
    private HttpClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private ResponseErrorHandlerInterface $responseErrorHandler;
    private SerializerInterface $serializer;

    public function __construct(HttpClientInterface $httpClient, SerializerInterface $serializer, array $interceptors = [])
    {
        $this->httpClient = $httpClient;
        $this->setRequestFactory(new DefaultRequestFactory());
        $this->setInterceptors($interceptors);
        $this->setSerializer($serializer);
        $this->setUriFactory(new DefaultUriFactory());
        $this->setResponseErrorHandler(new DefaultResponseErrorHandler());
    }

    public function setRequestFactory(RequestFactoryInterface $requestFactory): void
    {
        $this->requestFactory = $requestFactory;
    }

    public function setUriFactory(UriFactoryInterface $uriFactory): void
    {
        $this->uriFactory = $uriFactory;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array<RequestInterceptorInterface> $interceptors
     * @return void
     */
    public function setInterceptors(array $interceptors): void
    {
        $this->interceptors = $interceptors;
    }

    public function setResponseErrorHandler(ResponseErrorHandlerInterface $responseErrorHandler): void
    {
        $this->responseErrorHandler = $responseErrorHandler;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function intercept(RequestInterface $request, RequestExecutionInterface $execution): ResponseInterface
    {
        if ($execution->getContext()->has('request_value') && $request->getBody()->isWritable()) {
            $requestBody = $this->serializer->serialize($execution->getContext()->get('request_value'));
            $execution->getContext()->putReadOnly('request_body', $requestBody);
            $request->getBody()->write($requestBody);
        }
        return $this->processResponse($this->httpClient->sendRequest($request), $execution);
    }

    public function exchange(RequestInterface $request, ?RequestContext $context = null): ResponseInterface
    {
        $stack = new StackInterceptor($this, $this->interceptors);
        $execution = new DefaultRequestExecution($stack, $context);
        try {
            return $stack->next()->intercept($request, $execution);
        } catch (ClientExceptionInterface $exception) {
            throw RestClientException::fromClientException($exception);
        }
    }

    // GET

    public function get(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('GET', $uri, $uriVariables, $headers));
    }

    public function getForObject(string $uri, string $responseType, array $uriVariables = [], array $headers = []): ?object
    {
        return $this->exchangeForObject('GET', $uri, $responseType, null, $uriVariables, $headers);
    }

    public function getForList(string $uri, string $responseType, array $uriVariables = [], array $headers = []): array
    {
        return $this->exchangeForList('GET', $uri, $responseType, null, $uriVariables, $headers);
    }

    // POST

    public function post(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('POST', $uri, $uriVariables, $headers));
    }

    public function postForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): ?object
    {
        return $this->exchangeForObject('POST', $uri, $responseType, $body, $uriVariables, $headers);
    }

    public function postForList(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array
    {
        return $this->exchangeForList('POST', $uri, $responseType, $body, $uriVariables, $headers);
    }

    // PUT

    public function put(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('PUT', $uri, $uriVariables, $headers));
    }

    public function putForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): ?object
    {
        return $this->exchangeForObject('PUT', $uri, $responseType, $body, $uriVariables, $headers);
    }

    public function putForList(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array
    {
        return $this->exchangeForList('PUT', $uri, $responseType, $body, $uriVariables, $headers);
    }

    // PATCH

    public function patch(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('PATCH', $uri, $uriVariables, $headers));
    }

    public function patchForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): ?object
    {
        return $this->exchangeForObject('PATCH', $uri, $responseType, $body, $uriVariables, $headers);
    }

    public function patchForList(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array
    {
        return $this->exchangeForList('PATCH', $uri, $responseType, $body, $uriVariables, $headers);
    }

    // DELETE

    public function delete(string $uri, array $uriVariables = [], array $headers = []): void
    {
        $this->exchange($this->createRequest('DELETE', $uri, $uriVariables, $headers));
    }

    // COMMON

    private function exchangeForObject(string $method, string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): ?object
    {
        if (!\class_exists($responseType)) {
            throw new ResponseTypeNotFoundException($responseType);
        }
        $context = new RequestContext([
            'response_type' => $responseType
        ]);
        if ($body !== null) {
            $context->put('request_value', $body);
        }
        $this->exchange($this->createRequest($method, $uri, $uriVariables, $headers), $context);
        return $context->get('response_value');
    }

    private function exchangeForList(string $method, string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array
    {
        if (!\class_exists($responseType)) {
            throw new ResponseTypeNotFoundException($responseType);
        }
        $context = new RequestContext([
            'response_type' => $responseType,
            'response_as_list' => true,
        ]);
        if ($body !== null) {
            $context->put('request_value', $body);
        }
        $this->exchange($this->createRequest($method, $uri, $uriVariables, $headers), $context);
        return $context->get('response_value', []);
    }

    private function processResponse(ResponseInterface $response, RequestExecutionInterface $execution): ResponseInterface
    {
        if ($this->responseErrorHandler->hasError($response)) {
            $this->responseErrorHandler->handleError($response);
        }

        $responseBody = '';
        if ($this->hasBody($response, $execution)) {
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            // !!! ===============================  IMPORTANT NOTE   ================================ !!!
            // !!! A stream can be read only once.                                                    !!!
            // !!! Should be used RequestExecutionInterface::getResponseBody() to get a response body !!!
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $responseBody = $response->getBody()->getContents();
        }

        if (!empty($responseBody)) {
            $execution->getContext()->putReadOnly('response_body', $responseBody);
            $responseType = $execution->getContext()->get('response_type');
            $responseValue = $this->serializer->deserialize(
                $responseBody,
                $responseType,
                ['as_list' => $execution->getContext()->get('response_as_list', false)]
            );
            $execution->getContext()->put('response_value', $responseValue);
        }

        return $response;
    }

    private function hasBody(ResponseInterface $response, RequestExecutionInterface $execution): bool
    {
        $statusCode = $response->getStatusCode();
        return $statusCode !== 204 &&
            $statusCode!== 304 &&
            $statusCode !== 100 &&
            $statusCode !== 101 &&
            $statusCode !== 102 &&
            $statusCode !== 103 &&
            $execution->getContext()->has('response_type'); // <- We explicitly define a response type
    }

    private function createUri(string $uri, array $uriVariables): UriInterface
    {
        // Replace placeholders
        // /api/users/:id  + uriVariables['id'] => /api/users/1
        foreach($uriVariables as $key => $value){
            $placeholder = ':' . \strtolower($key);
            if (\strpos($uri, $placeholder) !== false) {
                $uri = str_replace($placeholder, $value, $uri);
                unset($uriVariables[$key]);
            }
        }

        if (empty($uriVariables)) {
            return $this->uriFactory->createUri($uri);
        }

        return $this->uriFactory->createUri($uri)->withQuery(\http_build_query($uriVariables));
    }

    private function createRequest(string $method, string $uri, array $uriVariables = [], array $headers = []): RequestInterface
    {
        $request = $this->requestFactory->createRequest($method, $this->createUri($uri, $uriVariables));
        foreach ($headers as $headerName => $headerValues) {
            $request = $request->withHeader($headerName, $headerValues);
        }
        return $request;
    }
}
