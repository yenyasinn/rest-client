<?php

namespace RestClient;

interface ContextInterface
{
    /**
     * A caller side payload object (object)
     */
    public const REQUEST_PAYLOAD = 'request_payload';

    /**
     * A serialized Http request body. (string)
     */
    public const REQUEST_BODY = 'request_body';

    /**
     * A Http response body. (string)
     */
    public const RESPONSE_BODY = 'response_body';

    /**
     * A full qualified class name. (string)
     */
    public const RESPONSE_TYPE = 'response_type';


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
}
