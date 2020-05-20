<?php

declare(strict_types=1);

namespace App\Tests\Part;

use App\Part\Domain\PartNumber;
use Generator;
use PHPUnit\Framework\TestCase;

final class PartNumberTest extends TestCase
{
    /**
     * @dataProvider data
     */
    public function testSanitize(?string $raw, string $expected): void
    {
        static::assertSame($expected, PartNumber::sanitize($raw));
    }

    public function data(): Generator
    {
        yield [null, ''];
        yield ['', ''];
        yield ['part', 'PART'];
        yield ['2part ', '2PART'];
        yield ['2p!@#$%^&*()art ', '2PART'];
        yield ['2ЗФКЕ', '2PART'];
    }
}
