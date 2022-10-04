<?php declare(strict_types=1);

namespace RestClient;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;
use RestClient\Configuration\ConfigurationInterface;
use RestClient\HttpHeaders\MediaType;
use RestClient\Interceptor\RequestInterceptorInterface;
use RestClient\Serialization\Symfony\JsonSymfonySerializer;
use RestClient\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DefaultJsonRestClient extends AbstractRestClient
{
    /**
     * @var array<NormalizerInterface|DenormalizerInterface>
     */
    private array $normalizers;

    /**
     * @param ConfigurationInterface $configuration
     * @param array<RequestInterceptorInterface> $interceptors
     * @param array<NormalizerInterface|DenormalizerInterface> $normalizers
     */
    public function __construct(ConfigurationInterface $configuration, array $interceptors = [], array $normalizers = [])
    {
        $this->normalizers = $normalizers;
        parent::__construct(
            $configuration,
            $interceptors
        );
        $this->getHttpHeaders()->setContentType(MediaType::APPLICATION_JSON);
    }

    protected function createHttpClient(ConfigurationInterface $configuration): ClientInterface
    {
        return new Client($this->prepareGuzzleConfig($configuration));
    }

    protected function createSerializer(ConfigurationInterface $configuration): SerializerInterface
    {
        return new JsonSymfonySerializer($this->normalizers);
    }

    private function prepareGuzzleConfig(ConfigurationInterface $configuration): array
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
