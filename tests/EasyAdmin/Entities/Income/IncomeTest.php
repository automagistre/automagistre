<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Income;

use App\Fixtures\Income\IncomeFixtures;
use App\Fixtures\Income\IncomePartFixtures;
use App\Tests\EasyAdminTestCase;

final class IncomeTest extends EasyAdminTestCase
{
    /**
     * @see \App\Income\Controller\IncomeController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'Income',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Income\Controller\IncomeController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'new',
            'entity' => 'Income',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Income\Controller\IncomeController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => IncomeFixtures::ID,
            'action' => 'edit',
            'entity' => 'Income',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Income\Controller\IncomeController::showAction()
     */
    public function testShow(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'show',
            'entity' => 'Income',
            'id' => IncomeFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Income\Controller\IncomeController::partAction()
     */
    public function testPart(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'part',
            'entity' => 'Income',
            'income_part_id' => IncomePartFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertRedirection($response, '/msk/?id=1ea8f183-f4b0-6fe6-aa61-5e6bd0ab745f&entity=Income&action=show');
    }

    /**
     * @see \App\Income\Controller\IncomeController::payAction()
     */
    public function testPay(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'pay',
            'entity' => 'Income',
            'id' => IncomeFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Income\Controller\IncomeController::accrueAction()
     */
    public function testAccrue(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'accrue',
            'entity' => 'Income',
            'id' => IncomeFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Income\Controller\IncomeController::supplyAction()
     */
    public function testSupply(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'supply',
            'entity' => 'Income',
            'id' => IncomeFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
