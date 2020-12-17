<?php

declare(strict_types=1);

namespace App\Tests\GraphQL;

use App\GraphQL\Www;
use Generator;
use GraphQL\Type\Schema;
use PHPUnit\Framework\TestCase;

final class SchemaAssertTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     *
     * @dataProvider schemas
     */
    public function test(Schema $schema): void
    {
        $schema->assertValid();
    }

    public function schemas(): Generator
    {
        yield [Www\Schema::create()];
    }
}
