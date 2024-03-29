<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Customer;

use App\Fixtures\Customer\PersonVasyaFixtures;
use App\Tests\EasyAdminTestCase;
use Generator;

final class PersonTest extends EasyAdminTestCase
{
    /**
     * @see \App\Customer\Controller\PersonController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'Person',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Customer\Controller\PersonController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'Person',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Customer\Controller\PersonController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'new',
            'entity' => 'Person',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Customer\Controller\PersonController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => PersonVasyaFixtures::ID,
            'action' => 'edit',
            'entity' => 'Person',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Customer\Controller\PersonController::showAction()
     */
    public function testShow(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => PersonVasyaFixtures::ID,
            'action' => 'show',
            'entity' => 'Person',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Customer\Controller\PersonController::widgetAction()
     */
    public function testWidget(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'widget',
            'entity' => 'Person',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Customer\Controller\PersonController::autocompleteAction()
     *
     * @dataProvider autocompletes
     */
    public function testAutocomplete(string $query): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'autocomplete',
            'entity' => 'Person',
            'query' => $query,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    public function autocompletes(): Generator
    {
        yield 'Vasya' => [substr(PersonVasyaFixtures::FIRSTNAME, 0, 3)];
        yield 'empty response' => ['bla'];
    }
}
