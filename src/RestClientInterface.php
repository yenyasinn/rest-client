<?php

namespace RestClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\HttpHeaders\HttpHeaders;

interface RestClientInterface
{
    public function exchange(RequestInterface $request, ?ContextInterface $context = null): ResponseInterface;

    // GET

    /**
     * @param string $uri
     * @param array<string, string> $uriVariables
     * @param string[][] $headers
     * @return ResponseInterface
     */
    public function get(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;

    /**
     * @param string $uri
     * @param string $responseType A single object: 'App\Model\User'. A list of objects: 'App\Mode\User[]'.
     * @param array<string, string> $uriVariables
     * @param string[][] $headers
     * @return array|object|null
     */
    public function getForObject(string $uri, string $responseType, array $uriVariables = [], array $headers = []): object|array|null;


    // POST

    /**
     * @param string $uri
     * @param array<string, string> $uriVariables
     * @param string[][] $headers
     * @return ResponseInterface
     */
    public function post(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;

    /**
     * @param string $uri
     * @param string $responseType A single object: 'App\Model\User'. A list of objects: 'App\Mode\User[]'.
     * @param ?object $body
     * @param array<string, string> $uriVariables
     * @param array $headers
     * @return array|object|null
     */
    public function postForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): object|array|null;


    // PUT

    /**
     * @param string $uri
     * @param array<string, string> $uriVariables
     * @param string[][] $headers
     * @return ResponseInterface
     */
    public function put(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;

    /**
     * @param string $uri
     * @param string $responseType A single object: 'App\Model\User'. A list of objects: 'App\Mode\User[]'.
     * @param ?object $body
     * @param array<string, string> $uriVariables
     * @param string[][] $headers
     * @return array|object|null
     */
    public function putForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): object|array|null;


    // PATCH

    /**
     * @param string $uri
     * @param array<string, string> $uriVariables
     * @param string[][] $headers
     * @return ResponseInterface
     */
    public function patch(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;

    /**
     * @param string $uri
     * @param string $responseType A single object: 'App\Model\User'. A list of objects: 'App\Mode\User[]'.
     * @param ?object $body
     * @param array<string, string> $uriVariables
     * @param string[][] $headers
     * @return array|object|null
     */
    public function patchForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): object|array|null;


    // DELETE

    /**
     * @param string $uri
     * @param array<string, string> $uriVariables
     * @param string[][] $headers
     * @return void
     */
    public function delete(string $uri, array $uriVariables = [], array $headers = []): void;

    public function getHttpHeaders(): HttpHeaders;
}
