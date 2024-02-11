<?php declare(strict_types=1);

namespace RestClient\Util;


use ReturnTypeWillChange;

class MultiValueMap implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array<string, string|string[]>
     */
    private array $map;

    /**
     * @var array<string, string>
     */
    private array $keys;

    private bool $caseInsensitive;


    public function __construct(bool $caseInsensitive = false)
    {
        $this->map = [];
        $this->keys = [];
        $this->caseInsensitive = $caseInsensitive;
    }

    public function setAll(array $map): self
    {
        $this->keys = $this->map = [];

        foreach ($map as $k => $v) {
            if (!\is_array($v)) {
                $v = [$v];
            }
            $normalizedKey = $this->normalizeKey($k);
            $this->map[$k] = $v;
            $this->keys[$normalizedKey] = $k;
        }

        return $this;
    }

    public function getAll(): array
    {
        return $this->map;
    }

    /**
     * @param iterable<string, string|string[]> $map
     * @return $this
     */
    public function addAll(iterable $map): self
    {
        foreach ($map as $k => $v) {
            $this->add($k, $v);
        }
        return $this;
    }

    /**
     * @param iterable<string, string|string[]> $map
     * @return $this
     */
    public function putAll(iterable $map): self
    {
        foreach ($map as $k => $v) {
            $this->put($k, $v);
        }
        return $this;
    }

    /**
     * @param iterable<string, string|string[]> $map
     * @return MultiValueMap
     */
    public function merge(iterable $map): MultiValueMap
    {
        return (clone $this)->putAll($map);
    }

    /**
     * Puts a value with a kye. If a key is present, overwrites that value.
     *
     * @param string $key
     * @param string|string[] $value
     * @return $this
     */
    public function put(string $key, $value): self
    {
        $normalizedKey = $this->normalizeKey($key);

        if (\is_array($value)) {
            $values = $value;
        } else {
            $values = [$value];
        }

        $this->map[$key] = $values;
        $this->keys[$normalizedKey] = $key;

        return $this;
    }

    /**
     * Adds new value to a key.
     *
     * @param string $key
     * @param string|string[] $value
     * @return $this
     */
    public function add(string $key, $value): self
    {
        $normalizedKey = $this->normalizeKey($key);

        if (\is_array($value)) {
            $values = $value;
        } else {
            $values = [$value];
        }

        if (\array_key_exists($normalizedKey, $this->keys)) {
            $originalKey = $this->keys[$normalizedKey];
            $this->map[$originalKey] = \array_merge($this->map[$originalKey], $values);
        } else {
            $this->map[$key] = $values;
            $this->keys[$normalizedKey] = $key;
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string|string[] $value
     * @return $this
     */
    public function putIfAbsent(string $key, $value): self
    {
        $normalizedKey = $this->normalizeKey($key);

        if (\array_key_exists($normalizedKey, $this->keys)) {
            return $this;
        }

        if (\is_array($value)) {
            $values = $value;
        } else {
            $values = [$value];
        }

        $this->map[$key] = $values;
        $this->keys[$normalizedKey] = $key;

        return $this;
    }

    public function remove(string $key): self
    {
        $normalizedKey = $this->normalizeKey($key);

        if (!\array_key_exists($normalizedKey, $this->keys)) {
            return $this;
        }

        $originalKey = $this->keys[$normalizedKey];
        unset($this->map[$originalKey], $this->keys[$normalizedKey]);

        return $this;
    }

    public function contains(string $key): bool
    {
        return \array_key_exists($this->normalizeKey($key), $this->keys);
    }

    public function get(string $key): array
    {
        $normalizedKey = $this->normalizeKey($key);

        if (!\array_key_exists($normalizedKey, $this->keys)) {
            return [];
        }

        $originalKey = $this->keys[$normalizedKey];

        return $this->map[$originalKey];
    }

    /**
     * @return array<string>
     */
    public function keys(): array
    {
        return \array_keys($this->keys);
    }

    public function getFirst(string $key): ?string
    {
        return $this[$key][0] ?? null;
    }

    public function isCaseInsensitive(): bool
    {
        return $this->caseInsensitive;
    }

    public function getIterator(): \Traversable
    {
        // CaseInsensitive
        if ($this->isCaseInsensitive()) {
            return (function () {
                foreach ($this->keys as $normalizedKey => $originalKey) {
                    yield $originalKey => $this->map[$originalKey];
                }
            })();
        }

        // CaseSensitive
        return (function () {
            foreach ($this->keys as $normalizedKey => $originalKey) {
                yield $originalKey => $this->map[$normalizedKey];
            }
        })();
    }

    /**
     * @param string|int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->contains($offset);
    }


    #[ReturnTypeWillChange] public function offsetGet($offset)
    {
        $originalKey = $this->keys[$this->normalizeKey($offset)] ?? null;
        if (null === $originalKey) {
            return null;
        }
        return $this->map[$originalKey] ?? null;
    }

    /**
     * @param string|int $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $offset = (string)$offset;
        $normalizedKey = $this->normalizeKey($offset);
        $this->map[$offset] = $value;
        $this->keys[$normalizedKey] = $offset;
    }

    /**
     * @param string|int $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    protected function normalizeKey(string $key): string
    {
        if ($this->caseInsensitive) {
            return \strtolower($key);
        }
        return $key;
    }
}
