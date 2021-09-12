<?php

declare(strict_types=1);

namespace App\Fixtures\Tenant;

use App\Tenant\Entity\Group;
use App\Tenant\Entity\GroupId;
use App\Tenant\Entity\Tenant;
use App\Tenant\Entity\TenantId;
use App\Tenant\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class MskTenantFixtures extends Fixture implements OrderedFixtureInterface
{
    public const ID = '1ec13c1c-dadf-6094-9836-0242d7e06827';
    public const IDENTIFIER = 'msk';
    public const DISPLAY_NAME = 'СТО Москва';
    public const GROUP_ID = '1ec13c1d-58e1-6244-b276-0242d7e06827';
    public const GROUP_IDENTIFIER = self::IDENTIFIER;

    public function __construct(private State $state)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder(): int
    {
        return -1;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            new Group(
                GroupId::from(self::GROUP_ID),
                self::GROUP_IDENTIFIER,
            ),
        );

        $tenant = self::asEntity();

        $manager->persist($tenant);
        $manager->flush();

        $this->state->set($tenant);
    }

    public static function asEntity(): Tenant
    {
        return new Tenant(
            TenantId::from(self::ID),
            self::IDENTIFIER,
            GroupId::from(self::GROUP_ID),
            self::DISPLAY_NAME,
        );
    }
}
