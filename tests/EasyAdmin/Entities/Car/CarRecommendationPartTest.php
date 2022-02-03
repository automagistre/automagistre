<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Car;

use App\Fixtures\Car\RecommendationFixtures;
use App\Tests\EasyAdminTestCase;

final class CarRecommendationPartTest extends EasyAdminTestCase
{
    /**
     * @see \App\Car\Controller\RecommendationPartController::searchAction()
     */
    public function testSearch(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'query' => 'bla',
            'action' => 'search',
            'entity' => 'CarRecommendationPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Car\Controller\RecommendationPartController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => RecommendationFixtures::RECOMMENDATION_PART_ID,
            'action' => 'edit',
            'entity' => 'CarRecommendationPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Car\Controller\RecommendationPartController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'recommendation_id' => RecommendationFixtures::ID,
            'action' => 'new',
            'entity' => 'CarRecommendationPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Car\Controller\RecommendationPartController::substituteAction()
     */
    public function testSubstitute(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'substitute',
            'entity' => 'CarRecommendationPart',
            'id' => RecommendationFixtures::RECOMMENDATION_PART_ID,
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Car\Controller\RecommendationPartController::autocompleteAction()
     */
    public function testAutocomplete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'autocomplete',
            'entity' => 'CarRecommendationPart',
            'query' => 'bla',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Car\Controller\RecommendationPartController::deleteAction()
     */
    public function testDelete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'delete',
            'entity' => 'CarRecommendationPart',
        ]));

        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
    }
}
