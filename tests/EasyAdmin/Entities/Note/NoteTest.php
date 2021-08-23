<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Note;

use App\Fixtures\Customer\PersonVasyaFixtures;
use App\Tests\EasyAdminTestCase;

final class NoteTest extends EasyAdminTestCase
{
    /**
     * @see \App\Note\Controller\NoteController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'subject' => PersonVasyaFixtures::ID,
            'action' => 'new',
            'entity' => 'Note',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Note\Controller\NoteController::removeAction()
     */
    public function testRemove(): void
    {
        self::markTestSkipped('Need Fixture');

        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'remove',
            'entity' => 'Note',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
