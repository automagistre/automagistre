<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Part;

use App\Fixtures\Manufacturer\NissanFixture;
use App\Fixtures\Part\GasketFixture;
use App\Tests\EasyAdminTestCase;

final class PartSupplyTest extends EasyAdminTestCase
{
    /**
     * @see \App\Part\Controller\SupplyController::increaseAction()
     */
    public function testIncrease(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'part_id' => GasketFixture::ID,
            'action' => 'increase',
            'entity' => 'PartSupply',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Part\Controller\SupplyController::decreaseAction()
     */
    public function testDecrease(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'part_id' => GasketFixture::ID,
            'supplier_id' => NissanFixture::ID,
            'action' => 'decrease',
            'entity' => 'PartSupply',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
