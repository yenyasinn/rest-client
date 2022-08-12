<?php

namespace RestClient;

interface ContextInterface
{
    /**
     * A caller side request payload object. [object|null]
     * If it is defined at the context, will be serialized and written to a Http request body.
     */
    public const REQUEST_MODEL = 'request_model';

    /**
     * A serialized Http request body. [string|null]
     */
    public const REQUEST_BODY = 'request_body';

    /**
     * A Http response body. [string|null]
     */
    public const RESPONSE_BODY = 'response_body';

    /**
     * A full qualified class name. [string]
     */
    public const RESPONSE_TYPE = 'response_type';

    /**
     * A caller side response payload object. [object|null]
     */
    public const RESPONSE_MODEL = 'response_model';


    /**
     * @param string $key
     * @param string|int|float|bool|array|object $value
     * @return ContextInterface
     */
    public function set(string $key, $value): ContextInterface;
    public function remove(string $key): ContextInterface;
    public function has(string $key): bool;

    /**
     * Returns NULL in case when a key is absent in a context.
     *
     * @param string $key
     * @param string|int|float|bool|array|object|null $default
     * @return string|int|float|bool|array|object|null
     */
    public function get(string $key, $default = null);
    public function all(): array;

    /**
     * @return array<string>
     */
    public function getKeys(): array;
}
