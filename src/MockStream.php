<?php

namespace RestClient;

use Psr\Http\Message\StreamInterface;

final class MockStream implements StreamInterface
{
    private string $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        return $this->data;
    }

    public function close(): void
    {
        // TODO: Not implemented
    }

    /**
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        return null;
    }

    public function getSize(): ?int
    {
        return \strlen($this->data);
    }

    public function tell(): int
    {
        throw new \RuntimeException('Not implemented');
    }

    public function eof(): bool
    {
        return true;
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function rewind(): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write($string): int
    {
        throw new \RuntimeException('Not implemented');
    }

    public function isReadable(): bool
    {
        return false;
    }

    public function read($length): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getContents(): string
    {
        return $this->data;
    }

    public function getMetadata($key = null): null|string
    {
        return null;
    }
}
