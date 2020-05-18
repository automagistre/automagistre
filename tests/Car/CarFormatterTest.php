<?php

declare(strict_types=1);

namespace App\Tests\Car;

use App\Car\Entity\CarId;
use App\Car\Fixtures\EmptyCarFixtures;
use App\Car\Fixtures\Primera2004Fixtures;
use App\Tests\Shared\IdentifierTestCase;
use Generator;

/**
 * @see \App\Car\View\CarFormatter
 */
final class CarFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [CarId::fromString(EmptyCarFixtures::ID), 'Не определено'];
        yield [CarId::fromString(Primera2004Fixtures::ID), 'Nissan Primera - P12 - 2004г.'];
    }
}
