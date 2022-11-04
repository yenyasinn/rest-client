<?php

namespace RestClient\Tests;

use PHPStan\Testing\TestCase;
use Psr\Http\Message\RequestInterface;
use RestClient\Configuration\DefaultConfiguration;
use RestClient\DefaultJsonRestClient;
use RestClient\RestClient;
use RestClient\RestClientInterface;
use RestClient\Serialization\Symfony\JsonSymfonySerializer;
use RestClient\Test\RequestHandler;
use RestClient\Test\TestClient;
use RestClient\Tests\Dto\MessageDto;
use RestClient\Tests\Dto\OrderDto;
use function RestClient\Helpers\asList;

class RestClientTest extends TestCase
{
    private static RestClientInterface $restClient;

    public static function setUpBeforeClass(): void
    {
        $handlers = [
            // equal:
            '/test/me' => '{"message": "ok"}',
            // equal:
            'equal:/test/1' => '{"message": "two"}',
            // match
            'match:/\/customer\/\d+\/orders/' => \json_encode([
                [
                    'order_id' => 1,
                    'name' => 'test order'
                ],
                [
                    'order_id' => 2,
                    'name' => 'book'
                ]
            ], JSON_THROW_ON_ERROR),
        ];

        $testClient = new TestClient(
            new RequestHandler(fn (RequestInterface $request) => '{"message": "nok"}'),
            $handlers
        );

        static::$restClient = new RestClient($testClient, new JsonSymfonySerializer());
    }

    public function testCreate(): void
    {
        $rest = new DefaultJsonRestClient(DefaultConfiguration::create('http://localhost:8000'));

        $this->assertEquals('application/json', $rest->getHttpHeaders()->getContentType());
    }

    public function testUriEquals(): void
    {
        /** @var MessageDto $msg */
        $msg = static::$restClient->getForObject('/test/me', MessageDto::class);

        $this->assertEquals('ok', $msg->getMessage());
    }

    public function testUriEqualsExplicit(): void
    {
        /** @var MessageDto $msg2 */
        $msg2 = static::$restClient->getForObject('/test/1', MessageDto::class);

        $this->assertEquals('two', $msg2->getMessage());
    }

    public function testUriMatches(): void
    {
        /** @var array<OrderDto>  $orders */
        $orders = static::$restClient->getForObject('/customer/123/orders', asList(OrderDto::class));

        $this->assertCount(2, $orders);
        $this->assertEquals(1, $orders[0]->getOrderId());
        $this->assertEquals('test order', $orders[0]->getName());
        $this->assertEquals(2, $orders[1]->getOrderId());
        $this->assertEquals('book', $orders[1]->getName());
    }
}
