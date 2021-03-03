<?php

declare(strict_types=1);

namespace App\Tests\Site;

use Generator;

final class ReviewTest extends GraphQlWwwTestCase
{
    public function data(): Generator
    {
        yield [
            <<<'GQL'
            {
                reviews {
                    nodes {
                        id
                        author
                        text
                        source
                        publishAt
                    }
                }
            }
            GQL,
            [],
            [
                'data' => [
                    'reviews' => [
                        'nodes' => [
                            [
                                'author' => 'Onotole',
                                'id' => '1eab71ba-d56d-65c6-8656-0242c0a8100a',
                                'publishAt' => '2019-12-25',
                                'source' => 'club',
                                'text' => 'Zaibatsu',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
