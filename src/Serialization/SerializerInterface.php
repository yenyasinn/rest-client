<?php

namespace RestClient\Serialization;

interface SerializerInterface
{
    public const AS_LIST = 'as_list';

    /**
     * @param object|array $data
     * @param array $context
     * @return string
     */
    public function serialize($data, array $context = []): string;

    /**
     * @param string $data
     * @param string $type
     * @param array $context
     * @return object|array
     */
    public function deserialize(string $data, string $type, array $context = []);
}
