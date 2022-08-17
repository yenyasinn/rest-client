<?php

namespace RestClient\Serialization;

final class NullSerializer implements SerializerInterface
{
    public function serialize(object $object): string
    {
        return '';
    }

    public function deserialize(string $data, string $targetType, bool $asList): \stdClass
    {
        return new \stdClass();
    }
}
