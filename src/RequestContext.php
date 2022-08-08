<?php declare(strict_types=1);

namespace RestClient;

final class RequestContext implements \Iterator
{
    /** @var array<string> */
    private array $keys;
    private int $keyIndex;
    private array $params;
    private array $readOnlyKeys;

    public function __construct(array $params = [])
    {
        $this->keys = [];
        $this->keyIndex = 0;
        $this->params = [];
        $this->readOnlyKeys = [];
        foreach ($params as $paramName => $paramValue) {
            $this->put($paramName, $paramValue);
        }
    }

    /**
     * @param string $name
     * @param int|string|float|bool|object|array $value
     * @param bool $readOnly
     * @return $this
     */
    public function put(string $name, $value, bool $readOnly = false): self
    {
        if (($this->readOnlyKeys[$name] ?? false) === true) {
            throw new \RuntimeException('Could not change read only field [' . $name . ']');
        }
        $this->checkValueType($value);
        $this->params[$name] = $value;
        if (!\array_key_exists($name, $this->params)) {
            $this->keys[] = $name;
        }
        if ($readOnly) {
            $this->readOnlyKeys[$name] = true;
        }
        return $this;
    }

    /**
     * @param string $name
     * @param int|string|float|bool|object|array $value
     * @return $this
     */
    public function putReadOnly(string $name, $value): self
    {
        return $this->put($name, $value, true);
    }

    /**
     * @param string $name
     * @param int|string|float|bool|object|array $default
     * @return int|string|float|bool|object|array|null
     */
    public function get(string $name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return ($this->params[$name] ?? null) !== null;
    }

    /**
     * @param string $name
     * @return int|string|float|bool|object|array|null
     */
    public function remove(string $name)
    {
        if ($this->has($name)) {
            $removedVal = $this->params[$name];
            unset($this->params[$name]);
            return $removedVal;
        }
        return null;
    }

    public function getAll(): array
    {
        return $this->params;
    }

    public function clear(): self
    {
        $this->keyIndex = 0;
        $this->keys = [];
        $this->params = [];
        $this->readOnlyKeys = [];
        return $this;
    }

    /**
     * @param mixed $value
     * @return void
     */
    private function checkValueType($value): void
    {
        $r = \is_string($value) ||
            \is_bool($value) ||
            \is_float($value) ||
            \is_int($value) ||
            \is_object($value) ||
            \is_array($value);
        if (!$r) {
            throw new \InvalidArgumentException('Bad value type [' . \gettype($value) . ']');
        }
    }

    /**
     * @return int|string|float|bool|object|array|null
     */
    public function current()
    {
        return $this->params[$this->key() ?? ''] ?? null;
    }

    public function next(): void
    {
        $this->keyIndex++;
    }

    /**
     * @return string|null
     */
    public function key(): ?string
    {
        return $this->keys[$this->keyIndex] ?? null;
    }

    public function valid(): bool
    {
        return null !== $this->current();
    }

    public function rewind(): void
    {
        $this->keyIndex = 0;
    }

    public function __toString(): string
    {
        return \json_encode($this->params, JSON_THROW_ON_ERROR);
    }
}

