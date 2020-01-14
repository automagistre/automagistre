<?php

declare(strict_types=1);

namespace App\Car\Fixtures;

use App\Car\Entity\Car;
use App\Car\Entity\Note;
use App\Enum\NoteType;
use App\User\Entity\User;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class NoteFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            CarFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['landlord'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $car = $this->getReference('car-1');
        assert($car instanceof Car);
        $user = $this->getReference('user-employee');
        assert($user instanceof User);

        $note = new Note($car, $user, NoteType::info(), 'Car Note');

        $manager->persist($note);
        $manager->flush();
    }
}
