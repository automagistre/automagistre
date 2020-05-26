<?php

declare(strict_types=1);

namespace App\Tests\Manufacturer;

use App\Manufacturer\Entity\ManufacturerId;
use App\Manufacturer\Fixtures\NissanFixture;
use App\Tests\Shared\IdentifierTestCase;
use Generator;

/**
 * @see \App\Manufacturer\View\ManufacturerFormatter
 */
final class ManufacturerFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [ManufacturerId::fromString(NissanFixture::ID), NissanFixture::NAME];
    }
}
