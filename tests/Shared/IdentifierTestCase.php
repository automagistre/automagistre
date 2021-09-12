<?php

declare(strict_types=1);

namespace App\Tests\Shared;

use App\Fixtures\Tenant\MskTenantFixtures;
use App\Identifier\IdentifierFormatter;
use App\Tenant\State;
use Generator;
use Premier\Identifier\Identifier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class IdentifierTestCase extends KernelTestCase
{
    /**
     * @dataProvider data
     */
    public function test(Identifier $identifier, string $expected, string $format = null): void
    {
        $container = self::getContainer();
        $container->get(State::class)->set(MskTenantFixtures::asEntity());

        $formatter = $container->get(IdentifierFormatter::class);

        self::assertSame($expected, $formatter->format($identifier, $format));
    }

    abstract public function data(): Generator;
}
