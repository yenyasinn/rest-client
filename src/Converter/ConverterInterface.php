<?php

namespace RestClient\Converter;

interface ConverterInterface
{
    public function canConvert(string $targetType, string $dataType): bool;

    /**
     * @param string $targetType
     * @param string$dataType
     * @param string $data
     * @return mixed
     */
    public function convert(string $targetType, string $dataType, string $data);
}
