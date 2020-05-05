<?php

declare(strict_types=1);

namespace App\Tests\Manufacturer;

use App\Manufacturer\Domain\ManufacturerId;
use App\Manufacturer\Infrastructure\Fixtures\NissanFixture;
use App\Tests\Infrastructure\Identifier\IdentifierTestCase;
use Generator;

/**
 * @see \App\Manufacturer\Infrastructure\ManufacturerFormatter
 */
final class ManufacturerFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [ManufacturerId::fromString(NissanFixture::ID), NissanFixture::NAME];
    }
}
