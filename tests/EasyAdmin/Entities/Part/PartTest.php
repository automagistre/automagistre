<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Part;

use App\Fixtures\Part\GasketFixture;
use App\Tests\EasyAdminTestCase;

final class PartTest extends EasyAdminTestCase
{
    /**
     * @see \App\Part\Controller\PartController::showAction()
     */
    public function testShow(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'show',
            'entity' => 'Part',
            'id' => GasketFixture::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Part\Controller\PartController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'Part',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Part\Controller\PartController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'new',
            'entity' => 'Part',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Part\Controller\PartController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => GasketFixture::ID,
            'action' => 'edit',
            'entity' => 'Part',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Part\Controller\PartController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'Part',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Part\Controller\PartController::widgetAction()
     */
    public function testWidget(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'widget',
            'entity' => 'Part',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Part\Controller\PartController::autocompleteAction()
     */
    public function testAutocomplete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'autocomplete',
            'entity' => 'Part',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
