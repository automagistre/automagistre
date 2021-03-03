<?php

declare(strict_types=1);

namespace App\Tests\Site;

use Generator;

final class StatsTest extends GraphQlWwwTestCase
{
    public function data(): Generator
    {
        yield [
            <<<'GQL'
                query {
                    stats {
                        orders
                        vehicles
                        customers {
                            persons
                            organizations
                        }
                        reviews
                    }
                }
                GQL,
            [],
            [
                'data' => [
                    'stats' => [
                        'orders' => 1,
                        'vehicles' => 1,
                        'customers' => [
                            'persons' => 1,
                            'organizations' => 0,
                        ],
                        'reviews' => 1,
                    ],
                ],
            ],
        ];
    }
}
