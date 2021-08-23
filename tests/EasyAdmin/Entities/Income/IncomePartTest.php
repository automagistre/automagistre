<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Income;

use App\Fixtures\Income\IncomeFixtures;
use App\Fixtures\Income\IncomePartFixtures;
use App\Tests\EasyAdminTestCase;

final class IncomePartTest extends EasyAdminTestCase
{
    /**
     * @see \App\Income\Controller\IncomePartController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'income_id' => IncomeFixtures::ID,
            'action' => 'new',
            'entity' => 'IncomePart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Income\Controller\IncomePartController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => IncomePartFixtures::ID,
            'action' => 'edit',
            'entity' => 'IncomePart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Income\Controller\IncomePartController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'IncomePart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Income\Controller\IncomePartController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'IncomePart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
