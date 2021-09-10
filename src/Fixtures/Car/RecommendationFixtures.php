<?php

declare(strict_types=1);

namespace App\Fixtures\Car;

use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationId;
use App\Car\Entity\RecommendationPart;
use App\Car\Entity\RecommendationPartId;
use App\Customer\Entity\OperandId;
use App\Fixtures\Part\GasketFixture;
use App\Part\Entity\PartId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;
use function assert;

final class RecommendationFixtures extends Fixture implements DependentFixtureInterface
{
    public const ID = '1eab7ad0-3f9c-65fa-874d-0242c0a81005';
    public const RECOMMENDATION_PART_ID = '1eab7ad3-b9df-6652-8b0d-0242c0a81005';
    public const PART_ID = GasketFixture::ID;

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

        $recommendation = new Recommendation(
            RecommendationId::from(self::ID),
            $car,
            'Test Service',
            new Money(100, new Currency('RUB')),
            OperandId::from('1ea9478c-eca4-6f96-a221-3ab8c77b35e5'),
        );

        $recommendation->addPart(new RecommendationPart(
            RecommendationPartId::from(self::RECOMMENDATION_PART_ID),
            $recommendation,
            PartId::from(self::PART_ID),
            1,
            new Money(100, new Currency('RUB')),
        ));

        $manager->persist($recommendation);
        $manager->flush();
    }
}
