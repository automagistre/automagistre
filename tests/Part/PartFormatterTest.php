<?php

declare(strict_types=1);

namespace App\Tests\Part;

use App\Fixtures\Part\GasketFixture;
use App\Part\Entity\PartId;
use App\Tests\Shared\IdentifierTestCase;
use Generator;

final class PartFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [PartId::from(GasketFixture::ID), 'Toyota - Сальник (PART1NUMBER)'];
        yield [PartId::from(GasketFixture::ID), 'PART1NUMBER - Toyota (Сальник)', 'autocomplete'];
    }
}
