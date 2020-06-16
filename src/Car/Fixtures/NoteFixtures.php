<?php

declare(strict_types=1);

namespace App\Car\Fixtures;

use App\Car\Entity\Car;
use App\Car\Entity\Note;
use App\Shared\Enum\NoteType;
use App\User\Entity\UserId;
use App\User\Fixtures\EmployeeFixtures;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class NoteFixtures extends Fixture implements DependentFixtureInterface
{
    public const CREATED_BY = EmployeeFixtures::ID;

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            EmptyCarFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $car = $this->getReference('car-1');
        assert($car instanceof Car);

        $note = new Note($car, UserId::fromString(self::CREATED_BY), NoteType::info(), 'Car Note');

        $manager->persist($note);
        $manager->flush();
    }
}
