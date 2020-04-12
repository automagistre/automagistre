<?php

declare(strict_types=1);

namespace App\Vehicle\Fixtures;

use App\Car\Entity\Model;
use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Fixtures\ManufacturerFixtures;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Generator;

final class ModelFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            ManufacturerFixtures::class,
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
        foreach ($this->generate() as [$manRef, $ref, $name]) {
            $manufacturer = $this->getReference($manRef);
            assert($manufacturer instanceof Manufacturer);

            $model = new Model();
            $model->name = $name;
            $model->manufacturer = $manufacturer;

            $this->addReference($ref, $model);

            $manager->persist($model);
        }

        $manager->flush();
    }

    public function generate(): Generator
    {
        yield ['manufacturer-1', 'model-1', 'GTR'];
        yield ['manufacturer-1', 'model-2', 'Primera'];
        yield ['manufacturer-1', 'model-3', 'Qashqai'];
    }
}
