<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Identifier;

use App\Doctrine\ORM\Type\Identifier;
use App\Infrastructure\Identifier\IdentifierFormatter;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class IdentifierTestCase extends KernelTestCase
{
    /**
     * @dataProvider data
     */
    public function test(Identifier $identifier, string $expected, string $format = null): void
    {
        self::bootKernel();

        $formatter = self::$container->get(IdentifierFormatter::class);

        static::assertSame($expected, $formatter->format($identifier, $format));
    }

    abstract public function data(): Generator;
}
