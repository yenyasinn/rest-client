<?php declare(strict_types=1);

use RestClient\Configuration\DefaultConfiguration;
use RestClient\Converter\JsonConverter;
use RestClient\DefaultJsonRestClient;
use RestClient\ResponseConverterExtractor;
use function RestClient\Helpers\asList;

/**
 * @see https://jsonplaceholder.typicode.com/
 */
class JsonPlaceholderClient extends DefaultJsonRestClient
{
    public function __construct()
    {
        parent::__construct(DefaultConfiguration::create('https://jsonplaceholder.typicode.com'), [], [new CommentNormalizer()]);
        $responseErrorHandler = $this->getResponseErrorHandler();
        if ($responseErrorHandler instanceof \RestClient\DefaultResponseErrorHandler) {
            /*
             * The response body will be converted to array.
             * Can be got by calling RestClientResponseException->getData().
             */
            $responseErrorHandler->setTargetType('array');
            $responseErrorHandler->setResponseExtractor(new ResponseConverterExtractor([new JsonConverter()]));
        }
    }

    public function getPosts(): array
    {
        return $this->getForObject('/posts', asList(BlogPost::class));
    }

    public function getPost(int $id): ?BlogPost
    {
        /** @var BlogPost|null $post */
        $post = $this->getForObject('/posts/:id', BlogPost::class, ['id' => $id]);
        return $post;
    }

    public function getComments(int $postId): array
    {
        return $this->getForObject('/posts/:post_id/comments', asList(BlogPost::class), ['post_id' => $postId]);
    }

    public function getComments2(int $postId): array
    {
        return $this->getForObject('/comments', asList(BlogPost::class), ['postId' => $postId]);
    }

    public function deletePost(int $postId): void
    {
        $this->delete('/posts/:post_id', ['post_id' => $postId]);
    }
}
