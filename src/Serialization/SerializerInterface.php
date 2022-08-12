<?php

namespace RestClient\Serialization;

use RestClient\Exception\UnknownTypeException;

interface SerializerInterface
{
    public function serialize(object $object): string;

    /**
     * @param string $data
     * @param string $targetType
     * @param bool $asList
     * @return object|array
     * @throws UnknownTypeException
     */
    public function deserialize(string $data, string $targetType, bool $asList);
}
