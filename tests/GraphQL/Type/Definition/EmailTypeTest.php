<?php

declare(strict_types=1);

namespace App\Tests\GraphQL\Type\Definition;

use App\GraphQL\Type\Definition\EmailType;
use Generator;
use PHPUnit\Framework\TestCase;

final class EmailTypeTest extends TestCase
{
    /**
     * @dataProvider data
     */
    public function test(string $input, string $expected): void
    {
        $type = new EmailType();

        self::assertSame($expected, $type->parseValue($input));
    }

    public function data(): Generator
    {
        yield ['ice210@mail.ru ', 'ice210@mail.ru'];
    }
}
