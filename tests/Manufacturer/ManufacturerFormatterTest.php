<?php

declare(strict_types=1);

namespace App\Tests\Manufacturer;

use App\Fixtures\Manufacturer\NissanFixture;
use App\Manufacturer\Entity\ManufacturerId;
use App\Tests\Shared\IdentifierTestCase;
use Generator;

/**
 * @see \App\Manufacturer\View\ManufacturerFormatter
 */
final class ManufacturerFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [ManufacturerId::from(NissanFixture::ID), NissanFixture::NAME];
    }
}
