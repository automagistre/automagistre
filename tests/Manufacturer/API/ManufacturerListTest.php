<?php

declare(strict_types=1);

namespace App\Tests\Manufacturer\API;

use App\JSONRPC\Test\JsonRPCTestCase;
use Generator;

/**
 * @see \App\Manufacturer\Ports\API\ManufacturerList
 */
final class ManufacturerListTest extends JsonRPCTestCase
{
    /**
     * @param mixed[] $request
     * @param mixed[] $expected
     *
     * @dataProvider success
     */
    public function testSuccess(array $request, array $expected): void
    {
        static::markTestSkipped('Wait for explode tenants');

        $client = self::createClient();

        $rpcResponse = $client->successJsonrpc('manufacturer.list', $request);

        static::assertSame($expected, $rpcResponse->getValue());
    }

    public function success(): Generator
    {
        yield 'all' => [
            [
            ],
            [
                'count' => 4,
                'list' => [
                    [
                        'id' => 1,
                        'localizedName' => 'Инфинити',
                        'name' => 'Infiniti',
                    ],
                    [
                        'id' => 2,
                        'localizedName' => 'Лексус',
                        'name' => 'Lexus',
                    ],
                    [
                        'id' => 3,
                        'localizedName' => 'Ниссан',
                        'name' => 'Nissan',
                    ],
                    [
                        'id' => 4,
                        'localizedName' => 'Тойота',
                        'name' => 'Toyota',
                    ],
                ],
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];

        yield 'IN' => [
            [
                'filtering' => [
                    ['field' => 'id', 'comparison' => 'IN', 'value' => ['3']],
                ],
                'ordering' => [
                    ['field' => 'name', 'direction' => 'desc'],
                ],
            ],
            [
                'count' => 1,
                'list' => [
                    [
                        'id' => 3,
                        'localizedName' => 'Ниссан',
                        'name' => 'Nissan',
                    ],
                ],
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];

        yield 'Page 3 Size 1' => [
            [
                'paging' => ['page' => 3, 'size' => 1],
            ],
            [
                'count' => 4,
                'list' => [
                    [
                        'id' => 3,
                        'localizedName' => 'Ниссан',
                        'name' => 'Nissan',
                    ],
                ],
                'paging' => ['page' => 3, 'size' => 1],
            ],
        ];

        yield 'EQ' => [
            [
                'filtering' => [
                    ['field' => 'name', 'comparison' => '=', 'value' => 'Lexus'],
                ],
                'ordering' => [
                    ['field' => 'name', 'direction' => 'desc'],
                    ['field' => 'localizedName', 'direction' => 'desc'],
                ],
            ],
            [
                'count' => 1,
                'list' => [
                    [
                        'id' => 2,
                        'localizedName' => 'Лексус',
                        'name' => 'Lexus',
                    ],
                ],
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];

        yield 'NOT IN' => [
            [
                'filtering' => [
                    ['field' => 'name', 'comparison' => 'NOT IN', 'value' => ['Nissan', 'Toyota', 'Lexus', 'Infiniti']],
                ],
            ],
            [
                'count' => 0,
                'list' => [],
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];
    }
}
