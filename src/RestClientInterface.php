<?php

namespace RestClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RestClientInterface
{
    public function exchange(RequestInterface $request, ?ContextInterface $context = null): ResponseInterface;

    // GET
    public function get(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;

    /**
     * @param string $uri
     * @param string $responseType A single object: 'App\Model\User'. A list of objects: 'App\Mode\User[]'.
     * @param array $uriVariables
     * @param array $headers
     * @return array|object|null
     */
    public function getForObject(string $uri, string $responseType, array $uriVariables = [], array $headers = []);

    // POST
    public function post(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;

    /**
     * @param string $uri
     * @param string $responseType A single object: 'App\Model\User'. A list of objects: 'App\Mode\User[]'.
     * @param ?object $body
     * @param array $uriVariables
     * @param array $headers
     * @return array|object|null
     */
    public function postForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []);

    // PUT
    public function put(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;

    /**
     * @param string $uri
     * @param string $responseType A single object: 'App\Model\User'. A list of objects: 'App\Mode\User[]'.
     * @param ?object $body
     * @param array $uriVariables
     * @param array $headers
     * @return array|object|null
     */
    public function putForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []);

    // PATCH
    public function patch(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;

    /**
     * @param string $uri
     * @param string $responseType A single object: 'App\Model\User'. A list of objects: 'App\Mode\User[]'.
     * @param ?object $body
     * @param array $uriVariables
     * @param array $headers
     * @return array|object|null
     */
    public function patchForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []);

    // DELETE
    public function delete(string $uri, array $uriVariables = [], array $headers = []): void;
}
