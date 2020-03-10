<?php

declare(strict_types=1);

namespace App\Calendar\Infrastructure\Fixtures;

use App\Calendar\Domain\CalendarEntry;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class CalendarFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['tenant'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $calendar = new CalendarEntry(
            new DateTimeImmutable('10:30'),
            new DateInterval('PT1H'),
            null,
            null
        );

        $manager->persist($calendar);
        $manager->flush();
    }
}
