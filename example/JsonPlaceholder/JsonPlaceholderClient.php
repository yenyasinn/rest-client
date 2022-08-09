<?php declare(strict_types=1);

use RestClient\Configuration\DefaultConfiguration;
use RestClient\JsonRestClient;

/**
 * @see https://jsonplaceholder.typicode.com/
 */
class JsonPlaceholderClient extends JsonRestClient
{
    public function __construct()
    {
        parent::__construct(DefaultConfiguration::create('https://jsonplaceholder.typicode.com'));
    }

    public function getPosts(): array
    {
        return $this->getForObject('/posts', \RestClient\Helpers\asList(BlogPost::class));
    }

    public function getPost(int $id): ?BlogPost
    {
        /** @var BlogPost|null $post */
        $post = $this->getForObject('/posts/:id', BlogPost::class, ['id' => $id]);
        return $post;
    }

    public function getComments(int $postId): array
    {
        return $this->getForObject('/posts/:post_id/comments', \RestClient\Helpers\asList(BlogPost::class), ['post_id' => $postId]);
    }

    public function getComments2(int $postId): array
    {
        return $this->getForObject('/comments', \RestClient\Helpers\asList(BlogPost::class), ['postId' => $postId]);
    }

    public function deletePost(int $postId): void
    {
        $this->delete('/posts/:post_id', ['post_id' => $postId]);
    }
}
