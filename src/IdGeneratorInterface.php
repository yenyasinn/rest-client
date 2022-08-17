<?php

namespace RestClient;

interface IdGeneratorInterface
{
    public function generate(): string;
}
