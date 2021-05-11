<?php

declare(strict_types=1);

namespace App\Tests\Customer;

use App\Customer\Entity\OperandId;
use App\Fixtures\Customer\OrganizationFixtures;
use App\Fixtures\Customer\PersonVasyaFixtures;
use App\Tests\Shared\IdentifierTestCase;
use Generator;

final class OperandFormatterTest extends IdentifierTestCase
{
    public function data(): Generator
    {
        yield [OperandId::from(OrganizationFixtures::ID), 'Org 1'];
        yield [OperandId::from(PersonVasyaFixtures::ID), 'Vasya'];
    }
}
