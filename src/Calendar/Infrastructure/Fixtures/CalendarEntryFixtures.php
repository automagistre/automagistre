<?php

declare(strict_types=1);

namespace App\Calendar\Infrastructure\Fixtures;

use App\Calendar\Domain\CalendarEntry;
use App\Calendar\Domain\CalendarEntryId;
use App\User\Domain\UserId;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use ReflectionClass;

final class CalendarEntryFixtures extends Fixture implements FixtureGroupInterface
{
    public const UUID = '3d8118b7-1773-452a-b3de-0f141b344001';

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
            UserId::generate(),
            null,
            null
        );

        $ref = new ReflectionClass($calendar);
        $propRef = $ref->getProperty('id');
        $propRef->setAccessible(true);
        $propRef->setValue($calendar, CalendarEntryId::fromString(self::UUID));

        $manager->persist($calendar);
        $manager->flush();
    }
}
