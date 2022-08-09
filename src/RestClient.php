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
use function RestClient\Helpers\ctxRequestGetPayload;
use function RestClient\Helpers\ctxRequestHasPayload;
use function RestClient\Helpers\ctxResponseAsList;
use function RestClient\Helpers\ctxResponseGetType;

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
     * @param HttpClientInterface $httpClient
     * @param SerializerInterface $serializer
     * @param array<RequestInterceptorInterface> $interceptors
     */
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
    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        if (ctxRequestHasPayload($context)) {
            if (!$request->getBody()->isWritable()) {
                throw new RestClientException('Could not write a request body');
            }
            $requestBody = $this->serializer->serialize(ctxRequestGetPayload($context));
            $request->getBody()->write($requestBody);
            $context->set(ContextInterface::REQUEST_BODY, $requestBody);
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

    public function getForObject(string $uri, string $responseType, array $uriVariables = [], array $headers = [])
    {
        return $this->doExchange('GET', $uri, $responseType, null, $uriVariables, $headers);
    }

    // POST

    public function post(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('POST', $uri, $uriVariables, $headers));
    }

    public function postForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = [])
    {
        return $this->doExchange('POST', $uri, $responseType, $body, $uriVariables, $headers);
    }

    // PUT

    public function put(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('PUT', $uri, $uriVariables, $headers));
    }

    public function putForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = [])
    {
        return $this->doExchange('PUT', $uri, $responseType, $body, $uriVariables, $headers);
    }

    // PATCH

    public function patch(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface
    {
        return $this->exchange($this->createRequest('PATCH', $uri, $uriVariables, $headers));
    }

    public function patchForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = [])
    {
        return $this->doExchange('PATCH', $uri, $responseType, $body, $uriVariables, $headers);
    }

    // DELETE

    public function delete(string $uri, array $uriVariables = [], array $headers = []): void
    {
        $this->exchange($this->createRequest('DELETE', $uri, $uriVariables, $headers));
    }

    // COMMON

    /**
     * @param string $method
     * @param string $uri
     * @param string $responseType
     * @param object|null $body
     * @param array $uriVariables
     * @param array $headers
     * @return array|object|null
     */
    private function doExchange(string $method, string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = [])
    {
        // Response types:
        // A single object  :    'App\Model\User'
        // A list of objects:    'App\Model\User[]'
        $responseAsList = \substr($responseType, -2) === '[]';
        if ($responseAsList) {
            $responseType = \rtrim($responseType, '[]');
        }

        if (!\class_exists($responseType)) {
            throw new ResponseTypeNotFoundException($responseType);
        }

        $context = (new Context())
            ->set(ContextInterface::RESPONSE_TYPE, $responseType)
            ->set('response_as_list', $responseAsList);

        if (null !== $body) {
            $context->set(ContextInterface::REQUEST_PAYLOAD, $body);
        }

        $this->exchange($this->createRequest($method, $uri, $uriVariables, $headers), $context);

        if ($responseAsList) {
            return $context->get('response_payload', []);
        }
        return $context->get('response_payload');
    }

    private function processResponse(ResponseInterface $response, ContextInterface $context): ResponseInterface
    {
        if ($this->responseErrorHandler->hasError($response)) {
            $this->responseErrorHandler->handleError($response);
        }

        $responseBody = '';
        if ($this->responseHasBody($response)) {
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            // !!! =========================  IMPORTANT NOTE   ============================== !!!
            // !!! A stream can be read only once.                                            !!!
            // !!! Should be used ContextInterface::get(RESPONSE_BODY) to get a response body !!!
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $responseBody = $response->getBody()->getContents();
        }

        if (!empty($responseBody) && $responseBody !== '{}') {
            $context->set(ContextInterface::RESPONSE_BODY, $responseBody);
            $responseValue = $this->serializer->deserialize(
                $responseBody,
                ctxResponseGetType($context),
                [SerializerInterface::AS_LIST => ctxResponseAsList($context)]
            );
            $context->set('response_payload', $responseValue);
        }

        return $response;
    }

    private function responseHasBody(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return $statusCode !== 204 &&
            $statusCode!== 304 &&
            $statusCode !== 100 &&
            $statusCode !== 101 &&
            $statusCode !== 102 &&
            $statusCode !== 103;
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
