<?php

declare(strict_types=1);

namespace App\Fixtures\Mc;

use App\Fixtures\Part\GasketFixture;
use App\MC\Entity\McLine;
use App\MC\Entity\McPart;
use App\Part\Entity\PartId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use function assert;

final class PartFixtures extends Fixture implements DependentFixtureInterface
{
    public const ID = '1eab7ad9-da2f-653c-bba9-0242c0a81005';

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            LineFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $line = $this->getReference('line-1');
        assert($line instanceof McLine);

        $mcPart = new McPart(
            Uuid::fromString(self::ID),
            $line,
            PartId::fromString(GasketFixture::ID),
            1,
            false
        );

        $this->addReference('mc-part-1', $mcPart);

        $manager->persist($mcPart);
        $manager->flush();
    }
}
