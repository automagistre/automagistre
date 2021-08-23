<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Part;

use App\Fixtures\Part\GasketFixture;
use App\Tests\EasyAdminTestCase;

final class PartCrossTest extends EasyAdminTestCase
{
    /**
     * @see \App\Part\Controller\PartCrossController::crossAction()
     */
    public function testCross(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'part_id' => GasketFixture::ID,
            'action' => 'cross',
            'entity' => 'PartCross',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Part\Controller\PartCrossController::uncrossAction()
     */
    public function testUncross(): void
    {
        self::markTestSkipped();

        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'uncross',
            'entity' => 'PartCross',
            'id' => GasketFixture::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
