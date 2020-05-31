<?php

namespace App\Tests\Calendar;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\EntryView;
use App\Calendar\Entity\OrderInfo;
use App\Calendar\Entity\Schedule;
use App\Calendar\View\Stream;
use App\Calendar\View\StreamOverflowException;
use App\Customer\Entity\OperandId;
use App\Customer\Fixtures\PersonVasyaFixtures;
use DateInterval;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    /**
     * @param EntryView[] $entities
     * @param EntryView[] $items
     *
     * @dataProvider validData
     */
    public function testHappyPath(array $entities, array $items): void
    {
        $stream = new Stream(null, $entities);

        foreach ($items as $key => $entity) {
            static::assertTrue($stream->has($key));
            static::assertSame($entity, $stream->get($key)->calendar);
        }
    }

    public function validData(): Generator
    {
        $entities = [];
        $items = [];

        $orderInfo = new OrderInfo(
            OperandId::fromString(PersonVasyaFixtures::ID),
            null,
            null,
            null,
        );

        $entities[] = $items['10:30'] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('10:30'), new DateInterval('PT30M')),
            $orderInfo,
        );
        $entities[] = $items['11:00'] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('11:00'), new DateInterval('PT1H')),
            $orderInfo,
        );

        $entities[] = $items['12:00'] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('12:00'), new DateInterval('PT2H30M')),
            $orderInfo,
        );

        $entities[] = $items['15:00'] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('15:00'), new DateInterval('PT30M')),
            $orderInfo,
        );

        yield [$entities, $items];
    }

    /**
     * @param EntryView[] $entities
     *
     * @dataProvider overflowData
     */
    public function testOverflow(array $entities): void
    {
        $this->expectException(StreamOverflowException::class);

        new Stream(null, $entities);
    }

    public function overflowData(): Generator
    {
        $entities = [];

        $orderInfo = new OrderInfo(
            OperandId::fromString(PersonVasyaFixtures::ID),
            null,
            null,
            null,
        );

        $entities[] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('10:30'), new DateInterval('PT1H')),
            $orderInfo,
        );

        $entities[] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('11:00'), new DateInterval('PT1H')),
            $orderInfo,
        );

        yield [$entities];
    }
}
