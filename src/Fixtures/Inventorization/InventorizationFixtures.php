<?php

declare(strict_types=1);

namespace App\Fixtures\Inventorization;

use App\Storage\Entity\Inventorization;
use App\Storage\Entity\InventorizationId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class InventorizationFixtures extends Fixture
{
    public const ID = '1ec043f5-0bd1-66fa-8b9d-0242ac1d000b';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $entity = new Inventorization(InventorizationId::from(self::ID));

        $manager->persist($entity);
        $manager->flush();
    }
}
