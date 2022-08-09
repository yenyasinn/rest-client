<?php declare(strict_types=1);

namespace RestClient\Configuration;

class DefaultConfiguration implements ConfigurationInterface
{
    private string $baseUri = '';
    private float $timeout = 5.0;
    private bool $allowRedirect = false;
    private ?string $proxy = null;

    public static function create(string $baseUri): DefaultConfiguration
    {
        return (new self())->setBaseUri($baseUri);
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function setBaseUri(string $baseUri): self
    {
        $this->baseUri = $baseUri;
        return $this;
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }

    public function setTimeout(float $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getAllowRedirect(): bool
    {
        return $this->allowRedirect;
    }

    public function setAllowRedirect(bool $allowRedirect): self
    {
        $this->allowRedirect = $allowRedirect;
        return $this;
    }

    public function getProxy(): ?string
    {
        return $this->proxy;
    }

    public function setProxy(?string $proxy): self
    {
        $this->proxy = $proxy;
        return $this;
    }
}
