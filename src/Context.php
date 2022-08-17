<?php declare(strict_types=1);

namespace RestClient;

class Context implements ContextInterface
{
    /**
     * @var array key => value
     */
    private array $values = [];

    public function set(string $key, $value): ContextInterface
    {
        $this->values[$key] = $value;
        return $this;
    }

    public function remove(string $key): ContextInterface
    {
        if (\array_key_exists($key, $this->values)) {
            unset($this->values[$key]);
        }
        return $this;
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->values);
    }

    public function get(string $key, $default = null)
    {
        return $this->values[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->values;
    }

    /**
     * @return array<string>
     */
    public function getKeys(): array
    {
        return \array_keys($this->values);
    }
}
