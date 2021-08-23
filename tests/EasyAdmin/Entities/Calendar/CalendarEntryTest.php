<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Calendar;

use App\Fixtures\Calendar\CalendarEntryFixtures;
use App\Tests\EasyAdminTestCase;

final class CalendarEntryTest extends EasyAdminTestCase
{
    /**
     * @see \App\Calendar\Controller\CalendarEntryController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'CalendarEntry',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Calendar\Controller\CalendarEntryController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'new',
            'entity' => 'CalendarEntry',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Calendar\Controller\CalendarEntryController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => CalendarEntryFixtures::ID,
            'action' => 'edit',
            'entity' => 'CalendarEntry',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
