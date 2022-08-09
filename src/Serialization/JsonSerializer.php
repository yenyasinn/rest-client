<?php declare(strict_types=1);

namespace RestClient\Serialization;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class JsonSerializer implements SerializerInterface
{
    public const PROP_SKIP_NULL_VALUES = 'skip_null_values';


    private array $properties;
    private ?NameConverterInterface $nameConverter;
    private ?SymfonySerializerInterface $innerSerializer;
    /** @var array<AbstractObjectMapper> */
    private array $objectMappers;

    /**
     * @param array<AbstractObjectMapper> $objectMappers
     * @param array $properties
     * @param NameConverterInterface|null $nameConverter
     */
    public function __construct(array $objectMappers = [], array $properties = [], ?NameConverterInterface $nameConverter = null)
    {
        $this->setObjectMappers($objectMappers);
        $this->properties = $properties;
        $this->nameConverter = $nameConverter;
        $this->innerSerializer = null;
    }

    public function setObjectMappers(array $objectMappers): void
    {
        $this->objectMappers = $objectMappers;
    }

    /**
     * @param object|array $data
     * @param array $context
     * @return string
     */
    public function serialize($data, array $context = []): string
    {
        return $this->getInnerSerializer()->serialize($data, 'json', $context);
    }

    /**
     * @param string $data
     * @param string $type
     * @param array $context
     * @return object|array
     */
    public function deserialize(string $data, string $type, array $context = [])
    {
        if (!\class_exists($type)) {
            throw new \RuntimeException('Class not found');
        }
        if (($context['as_list'] ?? false) === true) {
            $type .= '[]';
        }
        return $this->getInnerSerializer()->deserialize($data, $type, 'json', $context);
    }

    private function getInnerSerializer(): SymfonySerializerInterface
    {
        if (null === $this->innerSerializer) {
            // Create default serializer
            $mappers = $this->objectMappers;
            // A fallback normalizer
            $mappers[] = new ObjectNormalizer(
                null,
                $this->nameConverter,
                null,
                null,
                null,
                null,
                [AbstractObjectNormalizer::SKIP_NULL_VALUES => $this->properties[self::PROP_SKIP_NULL_VALUES] ?? false]
            );
            $mappers[] = new ArrayDenormalizer();
            $this->innerSerializer = new Serializer($mappers, [new JsonEncoder()]);
        }
        return $this->innerSerializer;
    }
}
