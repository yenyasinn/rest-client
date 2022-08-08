# RestClient

RestClient is an interface representing the main entry point for performing REST requests.

```php
interface RestClientInterface
{
    public function exchange(RequestInterface $request, ?RequestContext $context = null): ResponseInterface;

    // GET
    public function get(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;
    public function getForObject(string $uri, string $responseType, array $uriVariables = [], array $headers = []): ?object;
    public function getForList(string $uri, string $responseType, array $uriVariables = [], array $headers = []): array;

    // POST
    public function post(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;
    public function postForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): ?object;
    public function postForList(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array;

    // PUT
    public function put(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;
    public function putForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): ?object;
    public function putForList(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array;

    // PATCH
    public function patch(string $uri, array $uriVariables = [], array $headers = []): ResponseInterface;
    public function patchForObject(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): ?object;
    public function patchForList(string $uri, string $responseType, ?object $body = null, array $uriVariables = [], array $headers = []): array;

    // DELETE
    public function delete(string $uri, array $uriVariables = [], array $headers = []): void;
}
```
