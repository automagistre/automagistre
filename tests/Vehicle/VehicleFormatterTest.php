<?php

declare(strict_types=1);

namespace App\Tests\Vehicle;

use App\Fixtures\Vehicle\NissanGTRFixture;
use App\Fixtures\Vehicle\NissanPrimeraFixture;
use App\Fixtures\Vehicle\NissanQashqaiFixture;
use App\Tests\Shared\IdentifierTestCase;
use App\Vehicle\Entity\VehicleId;
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
