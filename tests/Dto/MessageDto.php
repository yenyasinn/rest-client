<?php

namespace RestClient\Tests\Dto;

class MessageDto
{
    private string $message = '';

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
