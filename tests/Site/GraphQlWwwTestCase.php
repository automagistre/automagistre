<?php

declare(strict_types=1);

namespace App\Tests\Site;

use App\Shared\Doctrine\Registry;
use App\Site\Context;
use App\Site\Schema;
use GraphQL\GraphQL;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Generator;
use function is_array;

abstract class GraphQlWwwTestCase extends KernelTestCase
{
    abstract public function data(): Generator;

    /**
     * @dataProvider data
     */
    public function test(string $query, array $variables, array $expected): void
    {
        $actual = self::executeQuery($query, $variables);

        self::deepSort($actual);
        self::deepSort($expected);

        self::assertSame($expected, $actual);
    }

    protected static function executeQuery(string $query, array $variableValues = null): array
    {
        self::bootKernel();

        $schema = Schema::create();
        $context = new Context(self::$container->get(Registry::class));

        $result = GraphQL::executeQuery($schema, $query, null, $context, $variableValues);

        return $result->toArray();
    }

    private static function deepSort(array &$array): void
    {
        ksort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                self::deepSort($value);
            }
        }
    }
}
