<?php

declare(strict_types=1);

namespace App\Tests\Order\EasyAdmin;

use App\Order\Fixtures\OrderFixtures;
use App\User\Fixtures\EmployeeFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SearchActionTest extends WebTestCase
{
    public function testRedirectOnNumberSearch(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => EmployeeFixtures::USERNAME,
            'PHP_AUTH_PW' => EmployeeFixtures::PASSWORD,
        ]);

        $client->request('GET', '/?entity=Order&action=search&query='.OrderFixtures::NUMBER);

        $response = $client->getResponse();

        static::assertTrue($response->isRedirect());
        static::assertSame('/?id='.OrderFixtures::ID.'&entity=Order&action=show', $response->headers->get('Location'));
    }
}
