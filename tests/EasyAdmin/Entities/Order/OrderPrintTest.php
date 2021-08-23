<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Order;

use App\Fixtures\Order\OrderFixtures;
use App\Tests\EasyAdminTestCase;

final class OrderPrintTest extends EasyAdminTestCase
{
    /**
     * @see \App\Order\Controller\OrderPrintController::matchingAction()
     */
    public function testMatching(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => OrderFixtures::ID,
            'action' => 'matching',
            'entity' => 'OrderPrint',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderPrintController::giveOutAction()
     */
    public function testGiveOut(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => OrderFixtures::ID,
            'action' => 'giveOut',
            'entity' => 'OrderPrint',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderPrintController::finishAction()
     */
    public function testFinish(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => OrderFixtures::ID,
            'action' => 'finish',
            'entity' => 'OrderPrint',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderPrintController::updAction()
     */
    public function testUpd(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => OrderFixtures::ID,
            'action' => 'upd',
            'entity' => 'OrderPrint',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderPrintController::invoiceAction()
     */
    public function testInvoice(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => OrderFixtures::ID,
            'action' => 'invoice',
            'entity' => 'OrderPrint',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
