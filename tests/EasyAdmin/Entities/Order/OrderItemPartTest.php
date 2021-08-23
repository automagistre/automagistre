<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Order;

use App\Fixtures\Order\OrderFixtures;
use App\Tests\EasyAdminTestCase;

final class OrderItemPartTest extends EasyAdminTestCase
{
    /**
     * @see \App\Order\Controller\OrderItemPartController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => OrderFixtures::PART_ID,
            'action' => 'edit',
            'entity' => 'OrderItemPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderItemPartController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'order_id' => OrderFixtures::ID,
            'action' => 'new',
            'entity' => 'OrderItemPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderItemPartController::relatedAction()
     */
    public function testRelated(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'related',
            'entity' => 'OrderItemPart',
            'id' => OrderFixtures::PART_ID,
        ]));

        $response = $client->getResponse();

        self::assertTrue($response->isRedirect());
    }

    /**
     * @see \App\Order\Controller\OrderItemPartController::reserveAction()
     */
    public function testReserve(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'reserve',
            'entity' => 'OrderItemPart',
            'id' => OrderFixtures::PART_ID,
        ]));

        $response = $client->getResponse();

        self::assertRedirection($response, '/msk/?action=list&entity=Order&menuIndex=3&submenuIndex=-1');
    }

    /**
     * @see \App\Order\Controller\OrderItemPartController::deReserveAction()
     */
    public function testDeReserve(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'deReserve',
            'entity' => 'OrderItemPart',
            'id' => OrderFixtures::PART_ID,
        ]));

        $response = $client->getResponse();

        self::assertRedirection($response, '/msk/?action=list&entity=Order&menuIndex=3&submenuIndex=-1');
    }
}
