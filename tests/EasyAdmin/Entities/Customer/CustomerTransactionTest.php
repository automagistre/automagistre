<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Customer;

use App\Fixtures\Customer\PersonVasyaFixtures;
use App\Tests\EasyAdminTestCase;

final class CustomerTransactionTest extends EasyAdminTestCase
{
    /**
     * @see \App\Customer\Controller\TransactionController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'CustomerTransaction',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Customer\Controller\TransactionController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'operand_id' => PersonVasyaFixtures::ID,
            'type' => 'increment',
            'action' => 'new',
            'entity' => 'CustomerTransaction',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Customer\Controller\TransactionController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'CustomerTransaction',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
