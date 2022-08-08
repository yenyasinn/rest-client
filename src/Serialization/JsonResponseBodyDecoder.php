<?php declare(strict_types=1);

namespace RestClient\Serialization;

use Psr\Http\Message\ResponseInterface;

final class JsonResponseBodyDecoder implements ResponseBodyDecoderInterface
{
    public function decode(string $data): array
    {
        return \json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    public function canDecode(ResponseInterface $response): bool
    {
        $contentType = $response->getHeaderLine('content-type');
        return \strpos($contentType, 'json') !== false;
    }
}
