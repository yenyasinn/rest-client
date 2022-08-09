<?php declare(strict_types=1);

namespace RestClient;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;
use RestClient\Configuration\ConfigurationInterface;
use RestClient\Serialization\AbstractObjectMapper;
use RestClient\Serialization\JsonSerializer;
use RestClient\Serialization\SerializerInterface;

class JsonRestClient extends RestClient
{
    public function __construct(ConfigurationInterface $configuration, array $objectMappers = [], array $interceptors = [])
    {
        parent::__construct(
            $this->createHttpClient($configuration),
            $this->createJsonSerializer($objectMappers),
            $interceptors
        );
    }

    private function createHttpClient(ConfigurationInterface $configuration): ClientInterface
    {
        return new Client($this->normalizeConfig($configuration));
    }

    /**
     * @param array<AbstractObjectMapper> $objectMappers
     * @return SerializerInterface
     */
    private function createJsonSerializer(array $objectMappers): SerializerInterface
    {
        return new JsonSerializer($objectMappers);
    }

    private function normalizeConfig(ConfigurationInterface $configuration): array
    {
        $config = [
            'base_uri' => $configuration->getBaseUri(),
            'allow_redirect' => $configuration->getAllowRedirect(),
        ];
        if ($configuration->getTimeout() > 0) {
            $config['timeout'] = $configuration->getTimeout();
        }
        if (null !== $configuration->getProxy()) {
            $config['proxy'] = $configuration->getProxy();
        }
        return $config;
    }
}
