<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\MC;

use App\Fixtures\Mc\LineFixtures;
use App\Fixtures\Mc\PartFixtures;
use App\Tests\EasyAdminTestCase;

final class McPartTest extends EasyAdminTestCase
{
    /**
     * @see \App\MC\Controller\PartController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'mc_line_id' => LineFixtures::ID,
            'action' => 'new',
            'entity' => 'McPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\MC\Controller\PartController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => PartFixtures::ID,
            'action' => 'edit',
            'entity' => 'McPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\MC\Controller\PartController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'McPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\MC\Controller\PartController::deleteAction()
     */
    public function testDelete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'delete',
            'entity' => 'McPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
    }
}
