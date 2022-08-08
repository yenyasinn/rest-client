<?php

namespace RestClient\Serialization;

use Psr\Http\Message\ResponseInterface;

interface ResponseBodyDecoderInterface
{
    public function decode(string $data): array;
    public function canDecode(ResponseInterface $response): bool;
}
