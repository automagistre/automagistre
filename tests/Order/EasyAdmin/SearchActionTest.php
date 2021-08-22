<?php

declare(strict_types=1);

namespace App\Tests\Order\EasyAdmin;

use App\Fixtures\Order\OrderFixtures;
use App\Fixtures\User\UserEmployeeFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SearchActionTest extends WebTestCase
{
    public function testRedirectOnNumberSearch(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => UserEmployeeFixtures::USERNAME,
            'PHP_AUTH_PW' => UserEmployeeFixtures::PASSWORD,
        ]);

        $client->request('GET', '/msk/?entity=Order&action=search&query='.OrderFixtures::NUMBER);

        $response = $client->getResponse();

        self::assertTrue($response->isRedirect());
        self::assertSame('/msk/?id='.OrderFixtures::ID.'&entity=Order&action=show', $response->headers->get('Location'));
    }
}
