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
                'list' => [
                    [
                        'id' => 1,
                        'name' => 'Infiniti',
                        'localizedName' => 'Инфинити',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Lexus',
                        'localizedName' => 'Лексус',
                    ],
                    [
                        'id' => 3,
                        'name' => 'Nissan',
                        'localizedName' => 'Ниссан',
                    ],
                    [
                        'id' => 4,
                        'name' => 'Toyota',
                        'localizedName' => 'Тойота',
                    ],
                ],
                'count' => 4,
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
                'list' => [
                    [
                        'id' => 3,
                        'name' => 'Nissan',
                        'localizedName' => 'Ниссан',
                    ],
                ],
                'count' => 1,
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];

        yield 'Page 3 Size 1' => [
            [
                'paging' => ['page' => 3, 'size' => 1],
            ],
            [
                'list' => [
                    [
                        'id' => 3,
                        'name' => 'Nissan',
                        'localizedName' => 'Ниссан',
                    ],
                ],
                'count' => 4,
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
                'list' => [
                    [
                        'id' => 2,
                        'name' => 'Lexus',
                        'localizedName' => 'Лексус',
                    ],
                ],
                'count' => 1,
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
                'list' => [],
                'count' => 0,
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];
    }
}
