<?php declare(strict_types=1);

use Psr\Http\Client\ClientInterface;
use RestClient\RestClient;
use RestClient\Serialization\DefaultJsonSerializer;
use RestClient\Serialization\SerializerInterface;

/**
 * @see https://jsonplaceholder.typicode.com/
 */
class JsonPlaceholderClient extends RestClient
{
    public function __construct()
    {
        parent::__construct($this->createHttpClient(), $this->createSerializer());
    }

    public function getPosts(): array
    {
        return $this->getForList('/posts', BlogPost::class);
    }

    public function getPost(int $id): ?BlogPost
    {
        /** @var BlogPost|null $post */
        $post = $this->getForObject('/posts/:id', BlogPost::class, ['id' => $id]);
        return $post;
    }

    public function getComments(int $postId): array
    {
        return $this->getForList('/posts/:post_id/comments', BlogPost::class, ['post_id' => $postId]);
    }

    public function getComments2(int $postId): array
    {
        return $this->getForList('/comments', BlogPost::class, ['postId' => $postId]);
    }

    public function deletePost(int $postId): void
    {
        $this->delete('/posts/:post_id', ['post_id' => $postId]);
    }

    private function createHttpClient(): ClientInterface
    {
        return new \GuzzleHttp\Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com/',
        ]);
    }

    private function createSerializer(): SerializerInterface
    {
        return new DefaultJsonSerializer();
    }
}
