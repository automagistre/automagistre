<?php

declare(strict_types=1);

namespace App\Tests\Review;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RedirectTest extends WebTestCase
{
    public function test(): void
    {
        $client = self::createClient(server: [
            'HTTP_HOST' => 'r.automagistre.ru',
        ]);

        $client->request('GET', '/msk/ymap');

        $response = $client->getResponse();

        self::assertTrue($response->isRedirection());
    }
}
