<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Order;

use App\Fixtures\Car\Primera2004Fixtures;
use App\Fixtures\Order\OrderFixtures;
use App\Tests\EasyAdminTestCase;

final class OrderItemServiceTest extends EasyAdminTestCase
{
    /**
     * @see \App\Order\Controller\OrderItemServiceController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'car_id' => Primera2004Fixtures::ID,
            'action' => 'list',
            'entity' => 'OrderItemService',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderItemServiceController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => OrderFixtures::SERVICE_ID,
            'action' => 'edit',
            'entity' => 'OrderItemService',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderItemServiceController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'order_id' => OrderFixtures::ID,
            'action' => 'new',
            'entity' => 'OrderItemService',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderItemServiceController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'car_id' => Primera2004Fixtures::ID,
            'action' => 'search',
            'entity' => 'OrderItemService',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderItemServiceController::autocompleteAction()
     */
    public function testAutocomplete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'textOnly' => '1',
            'action' => 'autocomplete',
            'entity' => 'OrderItemService',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderItemServiceController::recommendAction()
     */
    public function testRecommend(): void
    {
        $client = self::createClient();

        $client->request('POST', '/msk/?'.http_build_query([
            'action' => 'recommend',
            'entity' => 'OrderItemService',
            'id' => OrderFixtures::SERVICE_ID,
        ]));

        $response = $client->getResponse();

        self::assertRedirection($response, '/msk/?action=list&entity=OrderItemService'); // TODO редирект куда то не туда
    }

    /**
     * @see \App\Order\Controller\OrderItemServiceController::deleteAction()
     */
    public function testDelete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'delete',
            'entity' => 'OrderItemService',
            'id' => OrderFixtures::SERVICE_ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
    }
}
