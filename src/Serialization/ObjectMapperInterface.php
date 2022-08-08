<?php

namespace RestClient\Serialization;

interface ObjectMapperInterface
{
    public function toObject(array $data, array $context = []): object;
    public function toArray(object $object, array $context = []): array;
    public function canMapToObject(array $data, string $type, array $context = []): bool;
    public function canMapToArray(object $object, array $context = []): bool;
}
