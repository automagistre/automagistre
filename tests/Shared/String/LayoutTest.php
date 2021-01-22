<?php

declare(strict_types=1);

namespace App\Tests\Shared\String;

use App\Shared\String\Layout;
use Generator;
use PHPUnit\Framework\TestCase;

final class LayoutTest extends TestCase
{
    /**
     * @dataProvider switch
     */
    public function testSwitch(string $input, string $expected): void
    {
        self::assertSame($expected, Layout::switch($input));
    }

    public function switch(): Generator
    {
        yield ['', ''];
        yield ['part', 'зфке'];
        yield ['зфке', 'part'];
    }

    /**
     * @dataProvider english
     */
    public function testEnglish(string $input, string $expected): void
    {
        self::assertSame($expected, Layout::english($input));
    }

    public function english(): Generator
    {
        yield ['зфке', 'part'];
        yield ['Руддщ Цщкдв', 'Hello World'];
        yield ['бла', ',kf'];
    }
}
