<?php

declare(strict_types=1);

namespace App\Tests\Part;

use App\Part\Entity\PartId;
use App\Part\Fixtures\GasketFixture;
use App\Tests\Shared\IdentifierTestCase;
use Generator;

final class PartFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [PartId::fromString(GasketFixture::ID), 'Toyota - Сальник (PART1NUMBER)'];
        yield [PartId::fromString(GasketFixture::ID), 'PART1NUMBER - Toyota (Сальник)', 'autocomplete'];
    }
}
