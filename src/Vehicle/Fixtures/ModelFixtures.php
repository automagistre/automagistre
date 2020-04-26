<?php

declare(strict_types=1);

namespace App\Vehicle\Fixtures;

use App\Manufacturer\Domain\Manufacturer;
use App\Manufacturer\Infrastructure\Fixtures\NissanFixture;
use App\Vehicle\Domain\Model;
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
            NissanFixture::class,
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
            $manufacturer = $this->getReference(NissanFixture::REF);
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
        yield ['Nissan', 'model-1', 'GTR'];
        yield ['Nissan', 'model-2', 'Primera'];
        yield ['Nissan', 'model-3', 'Qashqai'];
    }
}
