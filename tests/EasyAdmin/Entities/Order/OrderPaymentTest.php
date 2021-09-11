<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Order;

use App\Fixtures\Order\OrderFixtures;
use App\Tests\EasyAdminTestCase;

final class OrderPaymentTest extends EasyAdminTestCase
{
    /**
     * @see \App\Order\Controller\OrderPaymentController::paymentAction()
     */
    public function testPayment(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'payment',
            'entity' => 'OrderPayment',
            'id' => OrderFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Order\Controller\OrderPaymentController::deleteAction()
     */
    public function testDelete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'delete',
            'entity' => 'OrderPayment',
        ]));

        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
    }
}
