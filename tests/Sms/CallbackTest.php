<?php

declare(strict_types=1);

namespace App\Tests\Sms;

use App\Sms\Controller\CallbackController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see CallbackController
 */
final class CallbackTest extends WebTestCase
{
    public function test(): void
    {
        $client = self::createClient(server: [
            'HTTP_HOST' => 'callback.automagistre.ru',
        ]);
        $client->request('GET', '/msk/smsaero/1eab64c5-18b0-646c-9ac3-0242c0a8100a');

        $response = $client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
