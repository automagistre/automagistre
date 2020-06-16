<?php

declare(strict_types=1);

namespace App\Tests\Vehicle\API;

use App\JSONRPC\Test\JsonRPCTestCase;
use App\Manufacturer\Fixtures\NissanFixture;
use App\Vehicle\Fixtures\NissanGTRFixture;
use App\Vehicle\Fixtures\NissanPrimeraFixture;
use App\Vehicle\Fixtures\NissanQashqaiFixture;
use Generator;

/**
 * @see \App\Vehicle\Ports\API\ModelList
 */
final class ModelListTest extends JsonRPCTestCase
{
    private const NISSAN_GTR = [
        'id' => NissanGTRFixture::ID,
        'localizedName' => null,
        'manufacturerId' => NissanGTRFixture::MANUFACTURER_ID,
        'model' => null,
        'name' => NissanGTRFixture::NAME,
        'yearFrom' => null,
        'yearTill' => null,
    ];
    private const NISSAN_PRIMERA = [
        'id' => NissanPrimeraFixture::ID,
        'localizedName' => null,
        'manufacturerId' => NissanPrimeraFixture::MANUFACTURER_ID,
        'model' => NissanPrimeraFixture::CASE_NAME,
        'name' => NissanPrimeraFixture::NAME,
        'yearFrom' => null,
        'yearTill' => null,
    ];
    private const NISSAN_QASHQAI = [
        'id' => NissanQashqaiFixture::ID,
        'localizedName' => NissanQashqaiFixture::LOCALIZED_NAME,
        'manufacturerId' => NissanQashqaiFixture::MANUFACTURER_ID,
        'model' => NissanQashqaiFixture::CASE_NAME,
        'name' => NissanQashqaiFixture::NAME,
        'yearFrom' => NissanQashqaiFixture::YEAR_FROM,
        'yearTill' => NissanQashqaiFixture::YEAR_TILL,
    ];

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
                    self::NISSAN_GTR,
                    self::NISSAN_PRIMERA,
                    self::NISSAN_QASHQAI,
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
                    self::NISSAN_QASHQAI,
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
                    self::NISSAN_QASHQAI,
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
                    self::NISSAN_PRIMERA,
                ],
                'paging' => ['page' => 1, 'size' => 50],
            ],
        ];

        yield 'NOT IN' => [
            [
                'filtering' => [
                    ['field' => 'manufacturerId', 'comparison' => 'NOT IN', 'value' => [NissanFixture::ID]],
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
