<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Order;

use App\Fixtures\Order\OrderFixtures;
use App\Tests\EasyAdminTestCase;

final class OrderCancelTest extends EasyAdminTestCase
{
    /**
     * @see \App\Order\Controller\OrderCancelController::cancelAction()
     */
    public function testCancel(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'cancel',
            'entity' => 'OrderCancel',
            'id' => OrderFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
