<?php

namespace App\Tests\Calendar;

use App\Calendar\Application\CalendarEntryView;
use App\Calendar\Application\Stream;
use App\Calendar\Application\StreamOverflowException;
use App\Calendar\Domain\CalendarEntryId;
use DateInterval;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    /**
     * @param CalendarEntryView[] $entities
     * @param CalendarEntryView[] $items
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

        $entities[] = $items['10:30'] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('10:30'),
            new DateInterval('PT30M')
        );
        $entities[] = $items['11:00'] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('11:00'),
            new DateInterval('PT1H'),
        );

        $entities[] = $items['12:00'] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('12:00'),
            new DateInterval('PT2H30M'),
        );

        $entities[] = $items['15:00'] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('15:00'),
            new DateInterval('PT30M'),
        );

        yield [$entities, $items];
    }

    /**
     * @param CalendarEntryView[] $entities
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

        $entities[] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('10:30'),
            new DateInterval('PT1H'),
        );

        $entities[] = new CalendarEntryView(
            CalendarEntryId::generate(),
            new DateTimeImmutable('11:00'),
            new DateInterval('PT1H'),
        );

        yield [$entities];
    }
}
