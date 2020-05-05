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
                'count' => 3,
                'list' => [
                    [
                        'id' => 1,
                        'localizedName' => null,
                        'manufacturerId' => 3,
                        'model' => null,
                        'name' => 'GTR',
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                    [
                        'id' => 2,
                        'localizedName' => null,
                        'manufacturerId' => 3,
                        'model' => null,
                        'name' => 'Primera',
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                    [
                        'id' => 3,
                        'localizedName' => null,
                        'manufacturerId' => 3,
                        'model' => null,
                        'name' => 'Qashqai',
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                ],
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
                'count' => 1,
                'list' => [
                    [
                        'id' => 1,
                        'localizedName' => null,
                        'manufacturerId' => 3,
                        'model' => null,
                        'name' => 'GTR',
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                ],
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
                'count' => 3,
                'list' => [
                    [
                        'id' => 3,
                        'localizedName' => null,
                        'manufacturerId' => 3,
                        'model' => null,
                        'name' => 'Qashqai',
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                ],
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
                'count' => 1,
                'list' => [
                    0 => [
                        'id' => 2,
                        'localizedName' => null,
                        'manufacturerId' => 3,
                        'model' => null,
                        'name' => 'Primera',
                        'yearFrom' => null,
                        'yearTill' => null,
                    ],
                ],
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];

        yield 'NOT IN' => [
            [
                'filtering' => [
                    ['field' => 'manufacturerId', 'comparison' => 'NOT IN', 'value' => ['3']],
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
