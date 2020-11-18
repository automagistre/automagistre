<?php

declare(strict_types=1);

namespace App\Tests\Appeal\Rest;

use Generator;
use Sentry\Util\JSON;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CreateTest extends WebTestCase
{
    /**
     * @dataProvider data
     */
    public function testHappyPath(string $url, array $data): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/appeal/'.$url, [], [], [], JSON::encode($data));
        $response = $client->getResponse();

        static::assertTrue($response->isSuccessful());
    }

    public function data(): Generator
    {
        yield 'Calculator' => [
            'calc',
            [
                'name' => 'bla',
                'phone' => '+79261680000',
                'body' => [
                    'bla',
                ],
            ],
        ];

        yield 'Schedule' => [
            'schedule',
            [
                'name' => 'bla',
                'phone' => '+79261680001',
                'date' => '2020-01-01',
            ],
        ];

        yield 'Question' => [
            'question',
            [
                'name' => 'bla',
                'email' => 'bla@automagistre.ru',
                'question' => 'bla bla',
            ],
        ];

        yield 'Cooperation' => [
            'cooperation',
            [
                'name' => 'bla',
                'phone' => '+79261680000',
            ],
        ];

        yield 'Tire Fitting' => [
            'tire-fitting',
            [
                'name' => 'bla',
                'phone' => '+79261680000',
                'body' => [
                    'bla',
                ],
            ],
        ];
    }
}
