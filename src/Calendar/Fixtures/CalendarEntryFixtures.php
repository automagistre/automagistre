<?php

declare(strict_types=1);

namespace App\Calendar\Fixtures;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\OrderInfo;
use App\Calendar\Entity\Schedule;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class CalendarEntryFixtures extends Fixture implements FixtureGroupInterface
{
    public const ID = '3d8118b7-1773-452a-b3de-0f141b344001';

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
        $calendar = CalendarEntry::create(
            CalendarEntryId::fromString(self::ID),
            new Schedule((new DateTimeImmutable('10:30'))->modify('+1 day'), new DateInterval('PT1H')),
            new OrderInfo(null, null, null, null),
        );

        $manager->persist($calendar);
        $manager->flush();
    }
}
