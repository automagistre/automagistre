<?php

declare(strict_types=1);

namespace App\Tests\Site;

use App\Fixtures\Manufacturer\NissanFixture;
use Generator;

final class VehiclesTest extends GraphQlWwwTestCase
{
    public function data(): Generator
    {
        $manufacturerId = NissanFixture::ID;

        yield [
            <<<GQL
            {
            vehicles (manufacturerId: "{$manufacturerId}") {
                id,
                name
                caseName
                localizedName
                manufacturer {
                    id
                    name
                    localizedName
                }
                production {
                    from
                    till
                }
            }
            }
            GQL,
            [],
            [
                'data' => [
                    'vehicles' => [
                        [
                            'caseName' => null,
                            'id' => '1ea88042-e4ff-6faa-80f4-ba1ca6d07248',
                            'localizedName' => null,
                            'manufacturer' => [
                                'id' => '1ea88058-1c1f-6f20-9482-ba1ca6d07248',
                                'localizedName' => 'Ниссан',
                                'name' => 'Nissan',
                            ],
                            'name' => 'GTR',
                            'production' => [
                                'from' => null,
                                'till' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
