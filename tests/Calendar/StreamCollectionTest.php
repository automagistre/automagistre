<?php

namespace App\Tests\Calendar;

use App\Calendar\Application\CalendarEntryView;
use App\Calendar\Application\Stream;
use App\Calendar\Application\StreamCollection;
use App\Calendar\Domain\CalendarEntryId;
use App\Entity\Landlord\Person;
use App\Entity\Tenant\Employee;
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
            static::assertSame($expected[$key]->worker, $value->worker);

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

        $worker1 = new Employee();
        $worker1->setPerson($person = new Person());
        $person->setFirstname('Vasya');
        $person->setLastname('Pupkin');

        $worker2 = new Employee();
        $worker2->setPerson($person = new Person());
        $person->setFirstname('Petr');
        $person->setLastname('Pushkin');

        $entity1 = $entities[] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('10:30'),
            new DateInterval('PT1H'),
            null,
            $worker1
        );

        $entity2 = $entities[] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('11:00'),
            new DateInterval('PT8H'),
            null,
            $worker1
        );

        $entity3 = $entities[] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('12:00'),
            new DateInterval('PT5H30M'),
            null,
            $worker2
        );

        $entity4 = $entities[] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('15:00'),
            new DateInterval('PT30M'),
        );

        $entity5 = $entities[] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('15:00'),
            new DateInterval('PT30M')
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
