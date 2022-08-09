<?php declare(strict_types=1);

namespace RestClient\Serialization;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractObjectMapper implements ObjectMapperInterface, NormalizerInterface, DenormalizerInterface
{
    private string $expectedType;
    /** @var array<string> */
    private array $requiredKeys;

    public function __construct(string $expectedType, array $requiredKeys = [])
    {
        $this->expectedType = $expectedType;
        $this->requiredKeys = $requiredKeys;
    }

    public function canMapToObject(array $data, string $type, array $context = []): bool
    {
        if ($type === $this->expectedType) {
            if (!empty($this->requiredKeys)) {
                $missedKeys = $this->requireKeys($data, $this->requiredKeys);
                throw new \RuntimeException(\sprintf('Missed required keys: [%s]', \implode(',', $missedKeys)));
            }
            return true;
        }
        return false;
    }

    public function canMapToArray(object $object, array $context = []): bool
    {
        return \is_a($object, $this->expectedType);
    }

    protected function requireKeys(array $data, array $requiredKeys, string $delimiter = '.'): array
    {
        $missedKeys = [];
        foreach ($requiredKeys as $key) {
            if (\strpos($key, $delimiter) === false) {
                if (!\array_key_exists($key, $data)) {
                    $missedKeys[] = $key;
                }
            } else {
                try {
                    $this->findByPath($key, $data, $delimiter);
                } catch (\OutOfRangeException $exception) {
                    $missedKeys[] = $key;
                }
            }
        }
        return $missedKeys;
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $delimiter
     * @return mixed
     */
    protected function findByPath(string $path, array $data, string $delimiter = '.')
    {
        $temp = &$data;
        $pathChunks = \explode($delimiter, $path);
        $pathLen = \count($pathChunks);
        $pos = 0;

        foreach ($pathChunks as $key) {
            if (\is_array($temp)) {
                if (!\array_key_exists($key, $temp)) {
                    throw new \OutOfRangeException(
                        \sprintf('Path not found `%s`', \implode($delimiter, $pathChunks))
                    );
                }
                $temp = &$temp[$key];
                $pos++;
            } elseif (\is_object($temp)) {
                if (! isset($temp->{$key})) {
                    throw new \OutOfRangeException(
                        \sprintf('Path not found `%s`', \implode($delimiter, $pathChunks))
                    );
                }
                $temp = &$temp->{$key};
                $pos++;
            }
        }

        if ($pathLen !== $pos) {
            throw new \OutOfRangeException(
                \sprintf('Path not found `%s`', \implode($delimiter, $pathChunks))
            );
        }

        return $temp;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (!\is_array($data)) {
            throw new \RuntimeException('Expected array');
        }
        return $this->toObject($data, $context);
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        if (!\is_array($data)) {
            throw new \RuntimeException('Expected array');
        }
        return $this->canMapToObject($data, $type, ['format' => $format]);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if (!\is_object($object)) {
            throw new \RuntimeException('Expected object');
        }
        return $this->toArray($object, $context);
    }

    public function supportsNormalization($data, string $format = null)
    {
        if (!\is_object($data)) {
            throw new \RuntimeException('Expected object');
        }
        return $this->canMapToArray($data, ['format' => $format]);
    }
}
