<?php

declare(strict_types=1);

namespace App\Tests\Vehicle;

use App\Tests\Shared\IdentifierTestCase;
use App\Vehicle\Entity\VehicleId;
use App\Vehicle\Fixtures\NissanGTRFixture;
use App\Vehicle\Fixtures\NissanPrimeraFixture;
use App\Vehicle\Fixtures\NissanQashqaiFixture;
use Generator;

/**
 * @see \App\Vehicle\View\VehicleFormatter
 */
final class VehicleFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [VehicleId::fromString(NissanGTRFixture::ID), 'Nissan GTR'];
        yield [VehicleId::fromString(NissanPrimeraFixture::ID), 'Nissan Primera - P12'];
        yield [VehicleId::fromString(NissanQashqaiFixture::ID), 'Nissan Qashqai - J10'];
        yield [VehicleId::fromString(NissanQashqaiFixture::ID), 'Nissan Qashqai - J10 (2006 - 2013)', 'long'];
    }
}
