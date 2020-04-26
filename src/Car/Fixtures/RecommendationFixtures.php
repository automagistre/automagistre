<?php

namespace App\Car\Fixtures;

use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationPart;
use App\Customer\Domain\Operand;
use App\Entity\Landlord\Part;
use App\Part\Fixtures\PartFixtures;
use App\User\Entity\User;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class RecommendationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            CarFixtures::class,
            PartFixtures::class,
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
        $worker = $this->getReference('person-1');
        assert($worker instanceof Operand);
        $user = $this->getReference('user-employee');
        assert($user instanceof User);

        $recommendation = new Recommendation(
            $car,
            'Test Service',
            new Money(100, new Currency('RUB')),
            $worker,
            $user
        );

        $part = $this->getReference('part-1');
        assert($part instanceof Part);

        $recommendation->addPart(new RecommendationPart(
            $recommendation,
            $part,
            1,
            new Money(100, new Currency('RUB')),
            $user
        ));

        $manager->persist($recommendation);
        $manager->flush();
    }
}
