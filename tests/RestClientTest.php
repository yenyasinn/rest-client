<?php

namespace RestClient\Tests;

use PHPUnit\Framework\TestCase;
use RestClient\Configuration\DefaultConfiguration;
use RestClient\DefaultJsonRestClient;
use RestClient\RestClient;
use RestClient\RestClientInterface;
use RestClient\Serialization\Symfony\JsonSymfonySerializer;
use RestClient\Testing\TestClient;
use RestClient\Tests\Dto\MessageDto;
use RestClient\Tests\Dto\OrderDto;
use function RestClient\Helpers\asList;

class RestClientTest extends TestCase
{
    private static RestClientInterface $restClient;

    public static function setUpBeforeClass(): void
    {
        $testClient = new TestClient([
            'GET?uri=/test/message' => '{"message": "ok"}',
            'GET?matcher=re&uri=/\/customer\/\d+\/orders/' => [
                'json' => [
                    [
                        'order_id' => 1,
                        'name' => 'test order'
                    ],
                    [
                        'order_id' => 2,
                        'name' => 'book'
                    ]
                ]
            ],
            'POST?uri=/customer/2/orders' => [
                'json' => [
                    'order_id' => 2,
                    'name' => 'car'
                ]
            ]
        ]);

        static::$restClient = new RestClient($testClient, new JsonSymfonySerializer());
    }

    public function testCreate(): void
    {
        $rest = new DefaultJsonRestClient(DefaultConfiguration::create('http://localhost:8000'));

        $this->assertEquals('application/json', $rest->getHttpHeaders()->getContentType());
    }

    public function testGetForObject(): void
    {
        /** @var MessageDto $msg */
        $msg = static::$restClient->getForObject('/test/message', MessageDto::class);

        $this->assertEquals('ok', $msg->getMessage());
    }

    public function testGetForObjectList(): void
    {
        /** @var array<OrderDto>  $orders */
        $orders = static::$restClient->getForObject('/customer/123/orders', asList(OrderDto::class));

        $this->assertCount(2, $orders);

        $this->assertEquals(1, $orders[0]->getOrderId());
        $this->assertEquals('test order', $orders[0]->getName());

        $this->assertEquals(2, $orders[1]->getOrderId());
        $this->assertEquals('book', $orders[1]->getName());
    }

    public function testPostForObject(): void
    {
        $newOrder = new OrderDto();
        $newOrder->setOrderId(2);
        $newOrder->setName('car');

        /** @var OrderDto $order */
        $order = static::$restClient->postForObject('/customer/2/orders', OrderDto::class, $newOrder);

        $this->assertEquals(2, $order->getOrderId());
        $this->assertEquals('car', $order->getName());
    }
}
