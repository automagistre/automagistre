<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\MC;

use App\Fixtures\Mc\EquipmentFixtures;
use App\Fixtures\Mc\LineFixtures;
use App\Tests\EasyAdminTestCase;

final class McLineTest extends EasyAdminTestCase
{
    /**
     * @see \App\MC\Controller\LineController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'mc_equipment_id' => EquipmentFixtures::ID,
            'action' => 'new',
            'entity' => 'McLine',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\MC\Controller\LineController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => LineFixtures::ID,
            'action' => 'edit',
            'entity' => 'McLine',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\MC\Controller\LineController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'McLine',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
