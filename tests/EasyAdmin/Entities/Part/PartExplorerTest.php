<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Part;

use App\Tests\EasyAdminTestCase;

final class PartExplorerTest extends EasyAdminTestCase
{
    /**
     * @see \App\Part\Controller\PartExplorerController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'PartExplorer',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
