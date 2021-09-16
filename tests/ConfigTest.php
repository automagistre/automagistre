<?php

declare(strict_types=1);

namespace App\Tests;

use Generator;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    /**
     * @dataProvider configs
     */
    public function testSort(string $path): void
    {
        /** @psalm-suppress UnresolvableInclude */
        $config = include $path;

        $expected = $config;
        ksort($expected);

        self::assertSame($expected, $config, 'Config must be sorted');
    }

    /**
     * @dataProvider configs
     */
    public function testNamespace(string $path): void
    {
        $config = file_get_contents($path);
        self::assertIsString($config);

        /** @var array<int, string> $lines */
        $lines = explode(PHP_EOL, $config);

        foreach ($lines as $line => $value) {
            self::assertFalse(
                str_starts_with(trim($value), '\\'),
                sprintf(
                    'Namespace MUST NOT contain leading slash at %s:%s: %s',
                    realpath($path),
                    $line + 1,
                    trim($value),
                ),
            );
        }
    }

    public function configs(): Generator
    {
        yield 'bundles.php' => [__DIR__.'/../config/bundles.php'];
        yield 'enums.php' => [__DIR__.'/../config/enums.php'];
    }
}
