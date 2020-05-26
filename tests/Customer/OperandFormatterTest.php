<?php

declare(strict_types=1);

namespace App\Tests\Customer;

use App\Customer\Entity\OperandId;
use App\Customer\Fixtures\OrganizationFixtures;
use App\Customer\Fixtures\PersonVasyaFixtures;
use App\Tests\Shared\IdentifierTestCase;
use Generator;

final class OperandFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [OperandId::fromString(OrganizationFixtures::ID), 'Org 1'];
        yield [OperandId::fromString(PersonVasyaFixtures::ID), 'Vasya'];
    }
}
