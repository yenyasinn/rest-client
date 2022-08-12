<?php declare(strict_types=1);

namespace RestClient\Serialization\Symfony;

use RestClient\Exception\UnknownTypeException;
use RestClient\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class JsonSymfonySerializer implements SerializerInterface
{
    private ?NameConverterInterface $nameConverter;
    private ?SymfonySerializerInterface $innerSerializer;
    /** @var array<NormalizerInterface|DenormalizerInterface> */
    private array $normalizers;
    private array $defaultContext;

    public function __construct(array $normalizers = [], array $defaultContext = [], ?NameConverterInterface $nameConverter = null)
    {
        $this->setNormalizers($normalizers);
        $this->defaultContext = $defaultContext;
        $this->nameConverter = $nameConverter;
        $this->innerSerializer = null;
    }

    /**
     * @param array<NormalizerInterface|DenormalizerInterface> $normalizers
     * @return void
     */
    public function setNormalizers(array $normalizers): void
    {
        $this->normalizers = $normalizers;
        $this->innerSerializer = null; // Force to re-create inner serializer
    }

    public function setDefaultContext(array $defaultContext): void
    {
        $this->defaultContext = $defaultContext;
    }

    public function setNameConverter(?NameConverterInterface $converter): void
    {
        $this->nameConverter = $converter;
        $this->innerSerializer = null; // Force to re-create inner serializer
    }

    public function serialize(object $object): string
    {
        return $this->getInnerSerializer()->serialize($object, 'json', $this->defaultContext);
    }

    /**
     * @param string $data
     * @param string $targetType
     * @param bool $asList
     * @return object|array
     * @throws UnknownTypeException
     */
    public function deserialize(string $data, string $targetType, bool $asList)
    {
        if (!\class_exists($targetType)) {
            throw new UnknownTypeException($targetType);
        }
        if ($asList) {
            $targetType .= '[]';
        }
        return $this->getInnerSerializer()->deserialize($data, $targetType, 'json', $this->defaultContext);
    }

    private function getInnerSerializer(): SymfonySerializerInterface
    {
        if (null === $this->innerSerializer) {
            $this->innerSerializer = new Serializer($this->buildNormalizers(), [new JsonEncoder()]);
        }
        return $this->innerSerializer;
    }

    private function buildNormalizers(): array
    {
        $normalizers = $this->normalizers;
        $normalizers[] = new ObjectNormalizer(null, $this->nameConverter);
        $normalizers[] = new ArrayDenormalizer();
        return $normalizers;
    }
}
