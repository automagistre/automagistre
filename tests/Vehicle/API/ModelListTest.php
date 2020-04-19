<?php

declare(strict_types=1);

namespace App\Tests\Vehicle\API;

use App\JSONRPC\Test\JsonRPCTestCase;
use Generator;

/**
 * @see \App\Vehicle\Ports\API\ModelList
 */
final class ModelListTest extends JsonRPCTestCase
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

        $rpcResponse = $client->successJsonrpc('vehicle.model.list', $request);

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
                        'manufacturerId' => 1,
                        'name' => 'GTR',
                        'localizedName' => null,
                        'model' => null,
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                    [
                        'id' => 2,
                        'manufacturerId' => 1,
                        'name' => 'Primera',
                        'localizedName' => null,
                        'model' => null,
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                    [
                        'id' => 3,
                        'manufacturerId' => 1,
                        'name' => 'Qashqai',
                        'localizedName' => null,
                        'model' => null,
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                ],
                'count' => 3,
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];

        yield 'IN' => [
            [
                'filtering' => [
                    ['field' => 'id', 'comparison' => 'IN', 'value' => ['1']],
                ],
                'ordering' => [
                    ['field' => 'name', 'direction' => 'desc'],
                ],
            ],
            [
                'list' => [
                    [
                        'id' => 1,
                        'manufacturerId' => 1,
                        'name' => 'GTR',
                        'localizedName' => null,
                        'model' => null,
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                ],
                'count' => 1,
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];

        yield 'Page 3 Size 1' => [
            [
                'paging' => ['page' => 3, 'size' => 1],
                'ordering' => [
                    ['field' => 'id', 'direction' => 'asc'],
                ],
            ],
            [
                'list' => [
                    [
                        'id' => 3,
                        'manufacturerId' => 1,
                        'name' => 'Qashqai',
                        'localizedName' => null,
                        'model' => null,
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                ],
                'count' => 3,
                'paging' => ['page' => 3, 'size' => 1],
            ],
        ];

        yield 'EQ' => [
            [
                'filtering' => [
                    ['field' => 'name', 'comparison' => '=', 'value' => 'Primera'],
                ],
                'ordering' => [
                    ['field' => 'name', 'direction' => 'desc'],
                    ['field' => 'id', 'direction' => 'desc'],
                ],
            ],
            [
                'list' => [
                    0 => [
                        'id' => 2,
                        'manufacturerId' => 1,
                        'name' => 'Primera',
                        'localizedName' => null,
                        'model' => null,
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                ],
                'count' => 1,
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];

        yield 'NOT IN' => [
            [
                'filtering' => [
                    ['field' => 'manufacturerId', 'comparison' => 'NOT IN', 'value' => ['1']],
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
