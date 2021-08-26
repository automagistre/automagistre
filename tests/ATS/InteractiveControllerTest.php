<?php

declare(strict_types=1);

namespace App\Tests\ATS;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function Safe\json_encode;

final class InteractiveControllerTest extends WebTestCase
{
    public function test(): void
    {
        $client = self::createClient(server: [
            'HTTP_HOST' => 'callback.automagistre.ru',
        ]);

        $client->request('POST', '/msk/uiscom/interactive', content: json_encode([
            'numa' => '79261680000',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{"returned_code":1}', $response->getContent());
    }
}
