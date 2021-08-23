<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use function sprintf;

abstract class EasyAdminTestCase extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        return parent::createClient($options, $server + [
            'PHP_AUTH_USER' => 'employee@automagistre.ru',
            'PHP_AUTH_PW' => 'pa$$word',
        ]);
    }

    public static function assertRedirection(Response $response, string $url): void
    {
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Redirection expected, but "%s" status code returned.', $response->getStatusCode()),
        );

        self::assertSame($url, $response->headers->get('Location'));
    }
}
