<?php declare(strict_types=1);

namespace RestClient;

use RestClient\Exception\UnknownTypeException;
use RestClient\Exception\RestClientException;
use RestClient\HttpHeaders\HttpHeaders;
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
use function RestClient\Helpers\ctx_request_get_model;
use function RestClient\Helpers\ctx_request_has_model;
use function RestClient\Helpers\ctx_response_as_list;
use function RestClient\Helpers\ctx_response_get_type;
use function RestClient\Helpers\response_has_message_body;

class RestClient implements RestClientInterface, RequestInterceptorInterface
{
    /** @var array<RequestInterceptorInterface> */
    private array $interceptors;
    private HttpClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private ResponseErrorHandlerInterface $responseErrorHandler;
    private SerializerInterface $serializer;
    /**
     * Default request headers.
     * @var HttpHeaders
     */
    private HttpHeaders $headers;

    /**
     * @param HttpClientInterface $httpClient
     * @param SerializerInterface $serializer
     * @param array<RequestInterceptorInterface> $interceptors
     * @param array $headers
     */
    public function __construct(HttpClientInterface $httpClient, SerializerInterface $serializer, array $interceptors = [], array $headers = [])
    {
        $this->httpClient = $httpClient;
        $this->setRequestFactory(new DefaultRequestFactory());
        $this->setUriFactory(new DefaultUriFactory());
        $this->setResponseErrorHandler(new DefaultResponseErrorHandler());
        $this->setInterceptors($interceptors);
        $this->setSerializer($serializer);
        $this->setHeaders($headers);
    }

    public function setHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
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

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param iterable<RequestInterceptorInterface> $interceptors
     * @return void
     */
    public function setInterceptors(iterable $interceptors): void
    {
        $this->interceptors = $interceptors;
    }

    public function pushInterceptor(RequestInterceptorInterface $interceptor): void
    {
        \array_unshift($this->interceptors, $interceptor);
    }

    /**
     * @return iterable<RequestInterceptorInterface>
     */
    public function getInterceptors(): iterable
    {
        return $this->interceptors;
    }

    public function setResponseErrorHandler(ResponseErrorHandlerInterface $responseErrorHandler): void
    {
        $this->responseErrorHandler = $responseErrorHandler;
    }

    public function getResponseErrorHandler(): ResponseErrorHandlerInterface
    {
        return $this->responseErrorHandler;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = new HttpHeaders($headers);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        if (ctx_request_has_model($context)) {

            // Are we able to write to a request stream?
            if (!$request->getBody()->isWritable()) {
                throw new RestClientException('Could not write a request body');
            }

            // Model -> string -> request, context
            $requestBody = $this->serializer->serialize(ctx_request_get_model($context));
            $context->set(ContextInterface::REQUEST_BODY, $requestBody);
            $request->getBody()->write($requestBody);
        }

        return $this->processResponse($this->httpClient->sendRequest($request), $context);
    }

    public function exchange(RequestInterface $request, ?ContextInterface $context = null): ResponseInterface
    {
        $stack = new StackInterceptor($this, $this->interceptors);
        try {
            return $stack->next()->intercept($request, $context ?? new Context(), new RequestExecution($stack));
        } catch (ClientExceptionInterface $exception) {
            throw RestClientException::fromClientException($exception);
        }
    }

    // GET

    public function get(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('GET', $uri, $uriVariables, $headers));
    }

    public function getForObject(string $uri, string $responseType, array $uriVariables = [], array $headers = []): array|null|object
    {
        return $this->doExchange('GET', $uri, $responseType, null, $uriVariables, $headers);
    }

    // POST

    public function post(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('POST', $uri, $uriVariables, $headers));
    }

    public function postForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array|null|object
    {
        return $this->doExchange('POST', $uri, $responseType, $body, $uriVariables, $headers);
    }

    // PUT

    public function put(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('PUT', $uri, $uriVariables, $headers));
    }

    public function putForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array|null|object
    {
        return $this->doExchange('PUT', $uri, $responseType, $body, $uriVariables, $headers);
    }

    // PATCH

    public function patch(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('PATCH', $uri, $uriVariables, $headers));
    }

    public function patchForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array|null|object
    {
        return $this->doExchange('PATCH', $uri, $responseType, $body, $uriVariables, $headers);
    }

    // DELETE

    public function delete(string $uri, array $uriVariables = [], array $headers = []): void
    {
        $this->exchange($this->createRequest('DELETE', $uri, $uriVariables, $headers));
    }

    // COMMON

    public function getHttpHeaders(): HttpHeaders
    {
        return $this->headers;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $responseType
     * @param object|null $body
     * @param array $uriVariables
     * @param array $headers
     * @return array|object|null
     */
    private function doExchange(string $method, string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): object|array|null
    {
        // Normalize a response type.
        // Response types:
        // A single object  :    'App\Model\User'
        // A list of objects:    'App\Model\User[]'
        $responseAsList = str_ends_with($responseType, '[]');
        if ($responseAsList) {
            $responseType = \rtrim($responseType, '[]');
        }

        if (!\class_exists($responseType)) {
            throw new UnknownTypeException($responseType);
        }

        $context = (new Context())
            ->set(ContextInterface::RESPONSE_TYPE, $responseType)
            ->set('response_as_list', $responseAsList);

        if (null !== $body) {
            $context->set(ContextInterface::REQUEST_MODEL, $body);
        }

        $this->exchange($this->createRequest($method, $uri, $uriVariables, $headers), $context);

        if ($responseAsList) {
            return $context->get(ContextInterface::RESPONSE_MODEL, []);
        }

        return $context->get(ContextInterface::RESPONSE_MODEL);
    }

    private function processResponse(ResponseInterface $response, ContextInterface $context): ResponseInterface
    {
        if ($this->responseErrorHandler->hasError($response)) {
            // At this point we MUST throw an exception
            $this->responseErrorHandler->handleError($response);
        }

        $this->processResponseBody($response, $context);

        return $response;
    }

    private function processResponseBody(ResponseInterface $response, ContextInterface $context): void
    {
        if (!response_has_message_body($response)) {
            return;
        }

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // !!! =========================  IMPORTANT NOTE   ============================== !!!
        // !!! A stream can be read only once.                                            !!!
        // !!! Should be used ContextInterface::get(RESPONSE_BODY) to get a response body !!!
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $responseBody = \trim($response->getBody()->getContents());

        if (empty($responseBody) || $responseBody === '{}') {
            return;
        }

        // Put a Http response body content (string) to a context
        $context->set(ContextInterface::RESPONSE_BODY, $responseBody);

        // Convert a Http response body content -> Model
        $responseModel = $this->serializer->deserialize(
            $responseBody,
            ctx_response_get_type($context),
            ctx_response_as_list($context)
        );

        if ($responseModel instanceof HttpResponseAwareInterface) {
            $responseModel->setHttpResponse(new ImmutableResponse(
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $response->getHeaders(),
                $responseBody
            ));
        }

        // Put model to context
        $context->set(ContextInterface::RESPONSE_MODEL, $responseModel);
    }

    private function createUri(string $uri, array $uriVariables): UriInterface
    {
        // Replace placeholders
        // /api/users/:id  + uriVariables['id'] => /api/users/1
        foreach($uriVariables as $key => $value){
            $placeholder = ':' . \strtolower($key);
            if (str_contains($uri, $placeholder)) {
                $value = is_string($value) ? $value : (string)$value;
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

        // Merge default request headers with context headers
        $headers = \array_merge($this->headers->getAll(), $headers);

        foreach ($headers as $headerName => $headerValues) {
            $request = $request->withHeader($headerName, $headerValues);
        }

        return $request;
    }
}
