<?php

namespace RestClient;

use Psr\Http\Client\ClientInterface;
use RestClient\Configuration\ConfigurationInterface;
use RestClient\Interceptor\RequestInterceptorInterface;
use RestClient\Serialization\SerializerInterface;

abstract class AbstractRestClient extends RestClient
{
    /**
     * @param ConfigurationInterface $configuration
     * @param array<RequestInterceptorInterface> $interceptors
     * @param array $headers
     */
    public function __construct(ConfigurationInterface $configuration, array $interceptors = [], array $headers = [])
    {
        parent::__construct(
            $this->createHttpClient($configuration),
            $this->createSerializer($configuration),
            $interceptors,
            $headers
        );
    }

    abstract protected function createHttpClient(ConfigurationInterface $configuration): ClientInterface;
    abstract protected function createSerializer(ConfigurationInterface $configuration): SerializerInterface;
}
