<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Utils\StringUtils;
use PHPUnit\Framework\TestCase;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class StringUtilsTest extends TestCase
{
    public function testIsRussia(): void
    {
        static::assertTrue(StringUtils::isRussian('текст'));
        static::assertFalse(StringUtils::isRussian('text'));
        static::assertTrue(StringUtils::isRussian('текст text'));
        static::assertTrue(StringUtils::isRussian('масло'));
    }
}
