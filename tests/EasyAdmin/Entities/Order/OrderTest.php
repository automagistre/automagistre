<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Order;

use App\Fixtures\Order\OrderFixtures;
use App\Order\Enum\OrderStatus;
use App\Tests\EasyAdminTestCase;

final class OrderTest extends EasyAdminTestCase
{
    /**
     * @see \App\Order\Controller\OrderController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'Order',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'new',
            'entity' => 'Order',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'Order',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderController::searchAction()
     */
    public function testRedirectOnNumberSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?entity=Order&action=search&query='.OrderFixtures::NUMBER);

        $response = $client->getResponse();

        self::assertTrue($response->isRedirect());
        self::assertSame('/msk/?id='.OrderFixtures::ID.'&entity=Order&action=show', $response->headers->get('Location'));
    }

    /**
     * @see \App\Order\Controller\OrderController::showAction()
     */
    public function testShow(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => OrderFixtures::ID,
            'action' => 'show',
            'entity' => 'Order',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderController::TOAction()
     */
    public function testTO(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'TO',
            'entity' => 'Order',
            'id' => OrderFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderController::suspendAction()
     */
    public function testSuspend(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'suspend',
            'entity' => 'Order',
            'order_id' => OrderFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderController::statusAction()
     */
    public function testStatus(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'status',
            'entity' => 'Order',
            'id' => OrderFixtures::ID,
            'status' => OrderStatus::notification()->toId(),
        ]));

        $response = $client->getResponse();

        self::assertRedirection($response, '/msk/?action=list&entity=Order');
    }
}
