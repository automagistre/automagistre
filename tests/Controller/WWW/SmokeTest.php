<?php

declare(strict_types=1);

namespace Controller\WWW;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SmokeTest extends WebTestCase
{
    /**
     * @dataProvider pagesProvider
     */
    public function testPages(string $url, int $statusCode): void
    {
        $client = self::createClient();
        $client->setServerParameter('HTTP_HOST', 'www.automagistre.ru');

        $client->request('GET', $url);
        $response = $client->getResponse();

        $this->assertSame($statusCode, $response->getStatusCode());
    }

    public function pagesProvider()
    {
        yield ['/', 200];
        yield ['/switch', 200];
        yield ['/repair', 200];
        yield ['/diagnostics/free', 200];
        yield ['/diagnostics/comp', 200];
        yield ['/tire', 200];
        yield ['/brands', 200];
        yield ['/corporates', 200];
        yield ['/price-list', 200];
        yield ['/maintenance', 200];
        yield ['/contacts', 200];
        yield ['/blog', 200];
        yield ['/blog/1', 200];
        yield ['/shop', 200];
        yield ['/privacy-policy', 200];
    }
}
