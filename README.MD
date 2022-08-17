# RestClient

RestClient is an interface representing the main entry point for performing REST requests.

### Examples

#### Create a new client

For simplicity, we are using a Guzzle HTTP client, but you are free to use any PSR-18 compliant HTTP client.

```php
$httpClient = new GuzzleHttp\Client([
    'base_uri' => 'https://animechan.vercel.app',
]);
$serializer = new \RestClient\Serialization\Symfony\JsonSymfonySerializer();

$rest = new \RestClient\RestClient($httpClient, $serializer);
```


#### Define an application response model
```php
class AnimeQuote
{
    private string $anime = '';
    private string $character = '';
    private string $quote = '';

    public function getAnime(): string
    {
        return $this->anime;
    }

    public function setAnime(string $anime): void
    {
        $this->anime = $anime;
    }

    public function getCharacter(): string
    {
        return $this->character;
    }

    public function setCharacter(string $character): void
    {
        $this->character = $character;
    }

    public function getQuote(): string
    {
        return $this->quote;
    }

    public function setQuote(string $quote): void
    {
        $this->quote = $quote;
    }
}
```


#### Get an object

Take a look at [the example](example/anime_quote.php).

```php
/** @var AnimeQuote|null $quote */
$quote = $rest->getForObject('/api/random', AnimeQuote::class);

print "------------------------------------------\n";
print 'Anime:     ' . $quote->getAnime() . "\n";
print 'Character: ' . $quote->getCharacter() . "\n";
print 'Quote:     ' . $quote->getQuote() . "\n";
print "------------------------------------------\n";
```


#### Get a list of objects

Take a look at [the example](example/JsonPlaceholder/print_blog_posts.php).

```php
/** @var array<ApiModel> $models */
$models = $rest->getForObject('/api/models', \RestClient\Helpers\asList(ApiModel::class));
```


#### Intercept request

Use case of interceptor:
- Request header modification
- Request and response logging
- Request denial based on certain request parameters
- Changing the request URL address

Take a look at [Log request interceptor](src/Interceptor/LogRequestInterceptor.php)
