<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Car;

use App\Fixtures\Car\Primera2004Fixtures;
use App\Fixtures\Car\RecommendationFixtures;
use App\Fixtures\Order\OrderFixtures;
use App\Tests\EasyAdminTestCase;

final class CarRecommendationTest extends EasyAdminTestCase
{
    /**
     * @see \App\Car\Controller\RecommendationController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => RecommendationFixtures::ID,
            'order_id' => OrderFixtures::ID,
            'action' => 'edit',
            'entity' => 'CarRecommendation',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Car\Controller\RecommendationController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'car_id' => Primera2004Fixtures::ID,
            'order_id' => OrderFixtures::ID,
            'action' => 'new',
            'entity' => 'CarRecommendation',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Car\Controller\RecommendationController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'CarRecommendation',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Car\Controller\RecommendationController::realizeAction()
     */
    public function testRealize(): void
    {
        $client = self::createClient();

        $client->request('POST', '/msk/?'.http_build_query([
            'action' => 'realize',
            'entity' => 'CarRecommendation',
            'id' => RecommendationFixtures::ID,
            'order_id' => OrderFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertRedirection($response, '/msk/?entity=Order&action=show&id='.OrderFixtures::ID);
    }

    /**
     * @see \App\Car\Controller\RecommendationController::autocompleteAction()
     */
    public function testAutocomplete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'autocomplete',
            'entity' => 'CarRecommendation',
            'query' => 'bla',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Car\Controller\RecommendationController::deleteAction()
     */
    public function testDelete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'delete',
            'entity' => 'CarRecommendation',
        ]));

        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
    }
}
