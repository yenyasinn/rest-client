<?php

require 'vendor/autoload.php';

// We are going to call a public API: 'https://animechan.vercel.app/api/random' which returns a random quote as JSON.
// Example:
// {
//	anime: 'Shiki',
//	character: 'Nao Yasumori',
//	quote: 'My new family is just so kind. It\'s almost if there\'s been some kind of mistake... like, I\'ll have to pay back for all this happiness later on.'
//}

// First of all let's create a response model
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

// Create REST client

// PSR-18 HTTP client
$httpClient = new GuzzleHttp\Client([
    'base_uri' => 'https://animechan.vercel.app',
]);

// Serializer (helps us to convert JSON -> PHP class)
$serializer = new \RestClient\Serialization\Symfony\JsonSymfonySerializer();

$rest = new \RestClient\RestClient($httpClient, $serializer);

/** @var AnimeQuote|null $quote */
$quote = $rest->getForObject('/api/random', AnimeQuote::class);

print "------------------------------------------\n";
print 'Anime:     ' . $quote->getAnime() . "\n";
print 'Character: ' . $quote->getCharacter() . "\n";
print 'Quote:     ' . $quote->getQuote() . "\n";
print "------------------------------------------\n";
