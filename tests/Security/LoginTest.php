<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Fixtures\User\UserEmployeeFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class LoginTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Войти', [
            '_username' => UserEmployeeFixtures::USERNAME,
            '_password' => UserEmployeeFixtures::PASSWORD,
        ]);

        $response = $client->getResponse();
        self::assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        self::assertSame('http://localhost/', $response->headers->get('location'));
    }
}
