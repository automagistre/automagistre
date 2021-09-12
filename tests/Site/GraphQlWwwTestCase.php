<?php

declare(strict_types=1);

namespace App\Tests\Site;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function is_array;

abstract class GraphQlWwwTestCase extends WebTestCase
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
        $client = self::createClient();

        $client->request('POST', '/msk/api/www', content: json_encode([
            'query' => $query,
            'variables' => $variableValues,
        ], JSON_THROW_ON_ERROR));

        $json = $client->getResponse()->getContent();

        self::assertIsString($json, 'api return false');

        return (array) json_decode($json, true, flags: JSON_THROW_ON_ERROR);
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
