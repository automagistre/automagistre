<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Storage;

use App\Fixtures\Inventorization\InventorizationFixtures;
use App\Tests\EasyAdminTestCase;

final class InventorizationTest extends EasyAdminTestCase
{
    /**
     * @see \App\Storage\Controller\InventorizationController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'Inventorization',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\InventorizationController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'new',
            'entity' => 'Inventorization',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\InventorizationController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'Inventorization',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\InventorizationController::showAction()
     */
    public function testShow(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'show',
            'entity' => 'Inventorization',
            'id' => InventorizationFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\InventorizationController::addPartAction()
     */
    public function testAddPart(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'addPart',
            'entity' => 'Inventorization',
            'id' => InventorizationFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\InventorizationController::editPartAction()
     */
    public function testEditPart(): void
    {
        self::markTestSkipped();

        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'editPart',
            'entity' => 'Inventorization',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\InventorizationController::removePartAction()
     */
    public function testRemovePart(): void
    {
        self::markTestSkipped();

        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'removePart',
            'entity' => 'Inventorization',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\InventorizationController::closeAction()
     */
    public function testClose(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'close',
            'entity' => 'Inventorization',
            'id' => InventorizationFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertRedirection($response, '/msk/?action=list&entity=Inventorization');
    }

    /**
     * @see \App\Storage\Controller\InventorizationController::leftoversAction()
     */
    public function testLeftovers(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => InventorizationFixtures::ID,
            'action' => 'leftovers',
            'entity' => 'Inventorization',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\InventorizationController::deleteAction()
     */
    public function testDelete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'delete',
            'entity' => 'Inventorization',
            'id' => InventorizationFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
    }
}
