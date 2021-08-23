<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Appeal;

use App\Appeal\Enum\AppealStatus;
use App\Fixtures\Appeal\AppealFixtures;
use App\Tests\EasyAdminTestCase;
use function http_build_query;

final class AppealTest extends EasyAdminTestCase
{
    /**
     * @see \App\Appeal\Controller\AppealController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'Appeal',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Appeal\Controller\AppealController::showAction()
     */
    public function testShow(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => AppealFixtures::ID,
            'action' => 'show',
            'entity' => 'Appeal',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Appeal\Controller\AppealController::statusAction()
     */
    public function testStatus(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'appeal_id' => AppealFixtures::ID,
            'action' => 'status',
            'entity' => 'Appeal',
            'status' => AppealStatus::inWork()->toId(),
        ]));

        $response = $client->getResponse();

        self::assertTrue($response->isRedirect());
        self::assertSame('/msk/?action=list&entity=Appeal', $response->headers->get('Location'));
    }
}
