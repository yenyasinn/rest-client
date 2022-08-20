<?php

namespace RestClient\IdGenerator;

interface IdGeneratorInterface
{
    public function generate(): string;
}
