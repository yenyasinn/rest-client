<?php

namespace RestClient\Configuration;

interface ConfigurationInterface
{
    public function getBaseUri(): string;
    public function getTimeout(): float;
    public function getAllowRedirect(): bool;
    public function getProxy(): ?string;
}
