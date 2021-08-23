<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Wallet;

use App\Fixtures\Wallet\WalletFixtures;
use App\Tests\EasyAdminTestCase;

final class WalletTest extends EasyAdminTestCase
{
    /**
     * @see \App\Wallet\Controller\WalletController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'Wallet',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Wallet\Controller\WalletController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'new',
            'entity' => 'Wallet',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Wallet\Controller\WalletController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => WalletFixtures::ID,
            'action' => 'edit',
            'entity' => 'Wallet',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
