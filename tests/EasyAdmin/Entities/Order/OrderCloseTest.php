<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Order;

use App\Fixtures\Order\OrderFixtures;
use App\Tests\EasyAdminTestCase;

final class OrderCloseTest extends EasyAdminTestCase
{
    /**
     * @see \App\Order\Controller\OrderCloseController::closeAction()
     */
    public function testClose(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'close',
            'entity' => 'OrderClose',
            'id' => OrderFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
