<?php declare(strict_types=1);

namespace RestClient\Serialization\Symfony;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class UcfirstNameConverter implements NameConverterInterface
{
    public function normalize(string $propertyName): string
    {
        return \ucfirst($propertyName);
    }

    public function denormalize(string $propertyName): string
    {
        return \lcfirst($propertyName);
    }
}
