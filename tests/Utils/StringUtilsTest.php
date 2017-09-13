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
        self::assertTrue(StringUtils::isRussian('текст'));
        self::assertFalse(StringUtils::isRussian('text'));
        self::assertTrue(StringUtils::isRussian('текст text'));
        self::assertTrue(StringUtils::isRussian('масло'));
    }
}
