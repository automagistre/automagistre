<?php

namespace App\Tests\Calendar;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\EntryView;
use App\Calendar\Entity\OrderInfo;
use App\Calendar\Entity\Schedule;
use App\Calendar\View\Stream;
use App\Calendar\View\StreamCollection;
use App\Employee\Entity\EmployeeId;
use DateInterval;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;

class StreamCollectionTest extends TestCase
{
    /**
     * @param Stream[] $expected
     *
     * @dataProvider validData
     */
    public function testHappyPath(StreamCollection $collection, array $expected): void
    {
        foreach ($collection as $key => $value) {
            static::assertSame($expected[$key]->workerId, $value->workerId);

            $items = $expected[$key]->all();
            foreach ($value->all() as $key2 => $item) {
                static::assertSame($items[$key2]->calendar, $item->calendar);
                static::assertSame($items[$key2]->length, $item->length);
            }
        }
    }

    public function validData(): Generator
    {
        $entities = [];

        $worker1 = EmployeeId::generate();
        $worker2 = EmployeeId::generate();

        $entity1 = $entities[] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('10:30'), new DateInterval('PT1H')),
            new OrderInfo(null, null, null, $worker1),
        );

        $entity2 = $entities[] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('11:00'), new DateInterval('PT8H')),
            new OrderInfo(null, null, null, $worker1),
        );

        $entity3 = $entities[] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('12:00'), new DateInterval('PT5H30M')),
            new OrderInfo(null, null, null, $worker2),
        );

        $entity4 = $entities[] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('15:00'), new DateInterval('PT30M')),
            new OrderInfo(null, null, null, null),
        );

        $entity5 = $entities[] = new EntryView(
            CalendarEntryId::generate(),
            new Schedule(new DateTimeImmutable('15:00'), new DateInterval('PT30M')),
            new OrderInfo(null, null, null, null),
        );

        $result = [
            new Stream($worker1, [$entity1, $entity4]),
            new Stream($worker1, [$entity2]),
            new Stream($worker2, [$entity3]),
            new Stream(null, [$entity5]),
        ];

        yield [new StreamCollection($entities), $result];
    }
}
