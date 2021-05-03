<?php

declare(strict_types=1);

namespace App\Tests\Car;

use App\Car\Entity\CarId;
use App\Fixtures\Car\EmptyCarFixtures;
use App\Fixtures\Car\Primera2004Fixtures;
use App\Tests\Shared\IdentifierTestCase;
use Generator;

/**
 * @see \App\Car\View\CarFormatter
 */
final class CarFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [CarId::from(EmptyCarFixtures::ID), 'Не определено'];
        yield [CarId::from(Primera2004Fixtures::ID), 'Nissan Primera - P12 - 2004г.'];
    }
}
