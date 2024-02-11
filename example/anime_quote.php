<?php

use Psr\Http\Message\ResponseInterface;
use RestClient\HttpResponseAwareInterface;

require 'vendor/autoload.php';

// We are going to call a public API: 'https://animechan.xyz/api/random' which returns a random quote as a JSON document.
// Example:
// {
//	anime: 'Shiki',
//	character: 'Nao Yasumori',
//	quote: 'My new family is just so kind. It\'s almost if there\'s been some kind of mistake... like, I\'ll have to pay back for all this happiness later on.'
//}

// First of all let's create a response model.
// If you need to get access to a ResponseInterface object, just implement HttpResponseAwareInterface (or use interceptor).
class AnimeQuote implements HttpResponseAwareInterface
{
    private ResponseInterface $response;
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

    public function getHttpResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function setHttpResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }
}

// Create REST client

// PSR-18 HTTP client
$httpClient = new GuzzleHttp\Client([
    'base_uri' => 'https://animechan.xyz',
]);

// Serializer (helps us to convert JSON -> PHP class)
$serializer = new \RestClient\Serialization\Symfony\JsonSymfonySerializer();

$rest = new \RestClient\RestClient($httpClient, $serializer);

/** @var AnimeQuote|null $quote */
$quote = $rest->getForObject('/api/random', AnimeQuote::class);

print "------------------------------------------\n";
print 'Anime:               ' . $quote->getAnime() . "\n";
print 'Character:           ' . $quote->getCharacter() . "\n";
print 'Quote:               ' . $quote->getQuote() . "\n";
print 'Raw response body:   ' . $quote->getHttpResponse()->getBody() . "\n";
print "------------------------------------------\n";
