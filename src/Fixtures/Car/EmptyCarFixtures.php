<?php

declare(strict_types=1);

namespace App\Fixtures\Car;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Note\Entity\Note;
use App\Note\Enum\NoteType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class EmptyCarFixtures extends Fixture
{
    public const ID = '1ea8818c-bf1b-6820-b45f-ba1ca6d07248';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $carId = CarId::from(self::ID);
        $car = new Car($carId);
        $this->addReference('car-1', $car);

        $manager->persist($car);

        $manager->persist(
            new Note($carId->toUuid(), NoteType::info(), 'Car Note'),
        );

        $manager->flush();
    }
}
