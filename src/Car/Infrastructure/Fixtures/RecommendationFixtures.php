<?php

namespace App\Car\Infrastructure\Fixtures;

use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationPart;
use App\Customer\Domain\Operand;
use App\Customer\Domain\OperandId;
use App\Customer\Infrastructure\Fixtures\PersonVasyaFixtures;
use App\Part\Domain\Part;
use App\Part\Domain\PartId;
use App\Part\Infrastructure\Fixtures\GasketFixture;
use App\User\Domain\UserId;
use App\User\Entity\User;
use App\User\Fixtures\EmployeeFixtures;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class RecommendationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const PART_ID = GasketFixture::ID;
    public const PART_SELECTOR_ID = EmployeeFixtures::ID;
    public const WORKER_ID = EmployeeFixtures::ID;
    public const CREATED_BY = EmployeeFixtures::ID;

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            EmptyCarFixtures::class,
            GasketFixture::class,
            PersonVasyaFixtures::class,
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
            OperandId::fromString(self::WORKER_ID),
            UserId::fromString(self::CREATED_BY),
        );

        $part = $this->getReference('part-1');
        assert($part instanceof Part);

        $recommendation->addPart(new RecommendationPart(
            $recommendation,
            PartId::fromString(self::PART_ID),
            1,
            new Money(100, new Currency('RUB')),
            UserId::fromString(self::PART_SELECTOR_ID),
        ));

        $manager->persist($recommendation);
        $manager->flush();
    }
}
